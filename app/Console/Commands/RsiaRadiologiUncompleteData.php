<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RsiaRadiologiUncompleteData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rsia:radiologi-uncomplete-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check uncomplete data (belum diisi hasil) from rsia radiologi, and send notification to dokter radiologi';

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

        $dokter = $spesialis->with(['dokter' => function ($q) {
            $q->select('kd_dokter', 'nm_dokter', 'kd_sps');
        }])->where('nm_sps', 'LIKE', '%radiologi%')->first();

        // get all permintaan with hasil. 
        $all_permintaan = $permintaan->with('hasil')
            ->where('tgl_sampel', date('Y-m-d'))
            ->orderBy('tgl_sampel', 'DESC')
            ->get();

        // count all_permintaantaan with hasil = null or hasil.hasil = ""
        $count = $all_permintaan->filter(function ($item) {
            return $item->hasil == null || $item->hasil->hasil == "";
        })->count();

        
        if ($count > 0) {
            $this->info("Total data: " . $count);
            foreach ($dokter->dokter as $k => $v) {
                $msg = \Kreait\Firebase\Messaging\CloudMessage::withTarget('topic', $v->kd_dokter)
                    ->withNotification([
                        'topic' => $v->kd_dokter,
                        'title' => 'Notifikasi Radiologi',
                        'body'  => 'Terdapat ' . $count . ' data pemeriksaan yang belum di analisa, mohon untuk segera dilakukan analisa',
                    ])->withData([
                        "kategori" => "Ranap",
                        "penjab" => "umum",
                        "action" => "radiologi-list",
                    ]);
    
                // send notification
                $messaging->send($msg);

                // log
                $this->info(date('Y-m-d H:i:s') . " : Notifikasi dikirim ke " . $v->kd_dokter);
    
                // sleep for 1 second
                sleep(1);
            }
        }

        return 1;
    }
}
