<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RsiaNotifUndangan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rsia:notif-undangan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to all recipient';

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

        $now = \Carbon\Carbon::now();
        $undangan = \App\Models\RsiaSuratInternal::with('penerima')
            ->whereHas('penerima')
            ->whereDate('tanggal', $now->format('Y-m-d'))->get();

        if (!$undangan) {
            $this->error($now->format('Y-m-d H:i:s') . " - No undangan found");
            return;
        }

        // looop through all undangan
        foreach ($undangan as $key => $und) {
            $c_u_tgl = \Carbon\Carbon::parse($und->tanggal);
            $diff = $c_u_tgl->diffInMinutes($now);

            // penerima
            $penerima = $und->penerima;

            // if difference 2 hours before the event send notf
            if ($diff == 120) {
                if ($penerima) {
                    // loop through all penerima
                    foreach ($penerima as $key => $p) {
                        // send notif to penerima
                        $msg = \Kreait\Firebase\Messaging\CloudMessage::withTarget('topic', $p->penerima)
                            ->withNotification([
                                'topic' => $p->penerima,
                                'title' => 'Reminder Undangan',
                                'body'  => 'Mengingatkan bahwa undangan ' . $und->perihal . ' akan dimulai dalam 2 jam lagi',
                            ])->withData([
                                'route' => 'undangan',
                                'kategori' => 'surat_internal',
                                'no_surat' => $und->no_surat,
                                'perihal' => $und->perihal,
                                'tempat' => $und->tempat,
                                'tanggal' => $und->tanggal,
                                'tgl_terbit' => $und->tgl_terbit,
                            ]);

                        $messaging->send($msg);

                        // log
                        $this->info($now->format('Y-m-d H:i:s') . " : 2 Hour check : Notifikasi dikirim ke " . $p->penerima);

                        // sleep for 1 second
                        sleep(1);
                    }
                }
            }

            // if difference 30 minutes before the event send notf
            if ($diff == 30) {
                if ($penerima) {
                    // loop through all penerima
                    foreach ($penerima as $key => $p) {
                        // send notif to penerima
                        $msg = \Kreait\Firebase\Messaging\CloudMessage::withTarget('topic', $p->penerima)
                            ->withNotification([
                                'topic' => $p->penerima,
                                'title' => 'Reminder Undangan',
                                'body'  => 'Mengingatkan kembali bahwa undangan perihal ' . $und->perihal . ' akan dimulai dalam 30 menit lagi, mohon untuk segera mempersiapkan diri',
                            ])->withData([
                                'route' => 'undangan',
                                'kategori' => 'surat_internal',
                                'no_surat' => $und->no_surat,
                                'perihal' => $und->perihal,
                                'tempat' => $und->tempat,
                                'tanggal' => $und->tanggal,
                                'tgl_terbit' => $und->tgl_terbit,
                            ]);

                        $messaging->send($msg);

                        // log
                        $this->info($now->format('Y-m-d H:i:s') . " 30 Minute check : Notifikasi dikirim ke " . $p->penerima);
                        
                        // sleep for 1 second
                        sleep(1);
                    }
                }
            }
        }

        return 1;
    }
}
