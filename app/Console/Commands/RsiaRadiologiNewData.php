<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RsiaRadiologiNewData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rsia:radiologi-new-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check new data from rsia radiologi';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $factory = (new \Kreait\Firebase\Factory)->withServiceAccount(base_path('firebase_credentials.json'));
        $messaging = $factory->createMessaging();


        $permintaan = new \App\Models\PermintaanRadiologi;
        $spesialis = new \App\Models\Spesialis;

        // permintaan count data where tgl_hasil dan jam_hasil = 0000-00-00 00:00:00 and save the result to file on project root directory named radiologi.conf
        $all = $permintaan->select("*")
            ->whereMonth("tgl_permintaan", date('m'))
            ->where('tgl_sampel', '!=', '0000-00-00')
            ->where('tgl_hasil', '!=', '0000-00-00')->count();

        $dokter = $spesialis->with(['dokter' => function ($q) {
            $q->select('kd_dokter', 'nm_dokter', 'kd_sps');
        }])->where('nm_sps', 'LIKE', '%radiologi%')->first();

        // save the result to file on public directory named radiologi.json
        // check if file not exist then create new file
        if (!file_exists(public_path('radiologi.json'))) {
            $file = fopen(public_path('radiologi.json'), 'w');
            fwrite($file, json_encode(['old_data' => $all]));
            fclose($file);
        } else {
            $fileContent = file_get_contents(public_path('radiologi.json'));
            $data = json_decode($fileContent, true);

            if ($all < $data['old_data']) {
                $data['old_data'] = $all;
                
                $file = fopen(public_path('radiologi.json'), 'w');
                fwrite($file, json_encode($data));
                fclose($file);
            }

            // compare the old data with new data
            if ($data['old_data'] != $all) {
                $gap = $all - $data['old_data'];

                // get the last gap data
                $pasien = $permintaan->with('hasil', 'gambar')
                    ->whereMonth("tgl_permintaan", date('m'))
                    ->where('tgl_sampel', '!=', '0000-00-00')
                    ->orderBy('tgl_permintaan', 'DESC')
                    ->orderBy('jam_permintaan', 'DESC')
                    ->limit($gap)->get();

                    
                $pasien = $pasien->filter(function ($value, $key) {
                    return ($value->gambar != null || $value->gambar->isNotEmpty()) && ($value->hasil == null || in_array($value->hasil->hasil, ['-', '', ' ']));
                });

                $this->info($pasien->count());

                if (!$pasien->isEmpty()) {
                    foreach ($pasien as $key => $value) {

                        $this->info($value->no_rawat);
                        $this->info($value->tgl_hasil);
                        $this->info($value->jam_hasil);
                        $this->newLine();

                        foreach ($dokter->dokter as $k => $v) {
                            $msg = \Kreait\Firebase\Messaging\CloudMessage::withTarget('topic', $v->kd_dokter)
                                ->withNotification([
                                    'topic' => $v->kd_dokter,
                                    'title' => 'TEST Notifikasi Pemeriksaan Radiologi',
                                    'body'  => 'Terdapat pasien baru dengan no rawat ' . $value->no_rawat . ' mohon untuk segera di cek',
                                ])->withData([
                                    "jam" => $value->jam_hasil,
                                    "tanggal" => $value->tgl_hasil,
                                    "no_rawat" => $value->no_rawat,
                                    "kategori" => $value->status,
                                    "penjab" => "umum",
                                    "action" => "radiologi",
                                ]);
    
                            // send notification
                            $messaging->send($msg);

                            // log
                            $this->info("Send notification to " . $v->kd_dokter);

                            // sleep for 3 second
                            sleep(3);
                        }
                    }
    
                    // updata the old data with new data
                    $data['old_data'] = $all;
    
                    // save the result to file on public directory named radiologi.json
                    $file = fopen(public_path('radiologi.json'), 'w');
                    fwrite($file, json_encode($data));
                    fclose($file);
                } else {
                    $data['old_data'] = $all;
                    
                    $file = fopen(public_path('radiologi.json'), 'w');
                    fwrite($file, json_encode($data));
                    fclose($file);
                }

            }
        }

        return 1;
    }
}
