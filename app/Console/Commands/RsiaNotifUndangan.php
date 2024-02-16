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
        $factory   = (new \Kreait\Firebase\Factory)->withServiceAccount(base_path('firebase_credentials.json'));
        $messaging = $factory->createMessaging();

        $now      = \Carbon\Carbon::now();
        $undangan = \App\Models\RsiaSuratInternal::with('penerima')
            ->whereHas('penerima')
            ->whereDate('tanggal', $now->format('Y-m-d'))->get();

        if (!$undangan) {
            $this->error($now->format('Y-m-d H:i:s') . " - No undangan found");
            return;
        }

        echo $now->format('Y-m-d H:i:s') . " - Found " . count($undangan) . " undangan\n";
        print_r($undangan->toArray());

          // looop through all undangan
        foreach ($undangan as $key => $und) {
            $c_u_tgl = \Carbon\Carbon::parse($und->tanggal);
            $diff    = $c_u_tgl->diffInMinutes($now);

              // penerima
            $penerima = $und->penerima;

              // if difference 2 hours before the event send notf
            if ($diff == 120) {
                $body  = "Mengingatkan bahwa undangan " . $und->perihal . "\n";
                $body .= "akan dimulai dalam 2 jam lagi." . "\n\n";
                $body .= "Tempat \t: " . $und->tempat . "\n";
                $body .= "Tanggal \t: " . \Carbon\Carbon::parse($und->tanggal)->isoFormat('dddd, D MMMM Y') . "\n";
                $body .= "Jam \t\t\t\t: " . \Carbon\Carbon::parse($und->tanggal)->isoFormat('HH:mm') . "\n";
                $body .= "Terimakasih.";

                if ($penerima) {
                      // loop through all penerima
                    foreach ($penerima as $key => $p) {
                          // send notif to penerima
                        $msg = \Kreait\Firebase\Messaging\CloudMessage::withTarget('topic', $p->penerima)
                            ->withNotification([
                                'topic' => $p->penerima,
                                'title' => 'Reminder Undangan 🔔',
                                'body'  => $body,
                            ])->withData([
                                'route'      => 'undangan',
                                'kategori'   => 'surat_internal',
                                'no_surat'   => $und->no_surat,
                                'perihal'    => $und->perihal,
                                'tempat'     => $und->tempat,
                                'tanggal'    => $und->tanggal,
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
                    $body  = "Mengingatkan bahwa undangan " . $und->perihal . "\n";
                    $body .= "akan dimulai dalam 30 menit lagi." . "\n\n";
                    $body .= "Tempat \t: " . $und->tempat . "\n";
                    $body .= "Tanggal \t: " . \Carbon\Carbon::parse($und->tanggal)->isoFormat('dddd, D MMMM Y') . "\n";
                    $body .= "Jam \t\t\t\t: " . \Carbon\Carbon::parse($und->tanggal)->isoFormat('HH:mm') . "\n";
                    $body .= "Terimakasih.";

                    foreach ($penerima as $key => $p) {
                          // send notif to penerima
                        $msg = \Kreait\Firebase\Messaging\CloudMessage::withTarget('topic', $p->penerima)
                            ->withNotification([
                                'topic' => $p->penerima,
                                'title' => 'Reminder Undangan 🔔',
                                'body'  => $body,
                            ])->withData([
                                'route'      => 'undangan',
                                'kategori'   => 'surat_internal',
                                'no_surat'   => $und->no_surat,
                                'perihal'    => $und->perihal,
                                'tempat'     => $und->tempat,
                                'tanggal'    => $und->tanggal,
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
