<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;
use Carbon\Carbon;

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
        $factory = (new Factory)->withServiceAccount(base_path('firebase_credentials.json'));
        $messaging = $factory->createMessaging();

        $now = Carbon::now();
        $undangan = \App\Models\RsiaSuratInternal::with('penerima')
            ->whereHas('penerima')
            ->whereDate('tanggal', $now->format('Y-m-d'))->get();

        if ($undangan->isEmpty()) {
            $this->error($now->format('Y-m-d H:i:s') . " - No undangan found");
            return;
        }

        $this->info($now->format('Y-m-d H:i:s') . " - " . $undangan->count() . " undangan found");
        
        foreach ($undangan as $und) {
            $c_u_tgl = Carbon::parse($und->tanggal);
            $diff = $c_u_tgl->diffInMinutes($now);

            if ($now > $c_u_tgl) {
                $this->info($now->format('Y-m-d H:i:s') . " - " . $und->perihal . " sudah berlalu");
                continue;
            }

            if ($diff == 120 || $diff == 30) {
                $body = "Mengingatkan bahwa undangan " . $und->perihal . "\n";
                $body .= "akan dimulai dalam " . ($diff == 120 ? "2 jam" : "30 menit") . " lagi." . "\n\n";
                $body .= "Tempat \t: " . $und->tempat . "\n";
                $body .= "Tanggal \t: " . $c_u_tgl->isoFormat('dddd, D MMMM Y') . "\n";
                $body .= "Jam \t\t\t\t: " . $c_u_tgl->isoFormat('HH:mm') . "\n";
                $body .= "Terimakasih.";

                if ($und->penerima) {
                    foreach ($und->penerima as $p) {
                        $msg = \Kreait\Firebase\Messaging\CloudMessage::withTarget('topic', $p->penerima)
                            ->withNotification([
                                'topic' => $p->penerima,
                                'title' => 'Reminder Undangan 🔔',
                                'body' => $body,
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
                        $this->info($now->format('Y-m-d H:i:s') . " " . ($diff == 120 ? "2 Hour check" : "30 Minute check") . ": Notifikasi dikirim ke " . $p->penerima);
                        sleep(1);
                    }
                }
            }
        }

        return 1;
    }
}