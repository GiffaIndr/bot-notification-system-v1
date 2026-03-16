<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendScheduledAnnouncements extends Command
{
    protected $signature   = 'announcements:send';
    protected $description = 'Kirim scheduled announcements via WhatsApp';

    public function handle()
    {
        $whatsapp = new WhatsAppService();

        // Cek koneksi WA dulu
        if (!$whatsapp->isConnected()) {
            $this->error('❌ WhatsApp service tidak terhubung!');
            return;
        }

        $now = Carbon::now();

        $announcements = Announcement::with(['group.members'])
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->get();

        if ($announcements->isEmpty()) {
            $this->info('Tidak ada announcement yang perlu dikirim.');
            return;
        }

        foreach ($announcements as $announcement) {

            // Ambil semua nomor WA member group
            $phones = $announcement->group->members()
                                          ->whereNotNull('phone')
                                          ->pluck('phone')
                                          ->toArray();

            if (empty($phones)) {
                $this->warn("Group {$announcement->group->name} tidak ada member dengan nomor WA.");
                continue;
            }

            // Format pesan
            $message = "📢 *{$announcement->title}*\n\n"
                     . "{$announcement->content}\n\n"
                     . "🏢 _{$announcement->group->name}_\n"
                     . "🕐 _" . $announcement->scheduled_at->format('d M Y, H:i') . "_";

            // Kirim ke semua member
            $result = $whatsapp->sendBulk($phones, $message);

            $this->info("✅ Terkirim ke group: {$announcement->group->name} ({$announcement->group->members()->count()} member)");

            // Update scheduled_at berdasarkan repeat
            if ($announcement->repeat !== 'none') {
                $next = match($announcement->repeat) {
                    'daily'   => $announcement->scheduled_at->copy()->addDay(),
                    'weekly'  => $announcement->scheduled_at->copy()->addWeek(),
                    'monthly' => $announcement->scheduled_at->copy()->addMonth(),
                };

                $announcement->update(['scheduled_at' => $next]);
                $this->info("🔁 Jadwal berikutnya: {$next->format('d M Y, H:i')}");

            } else {
                // Kalau tidak repeat, set scheduled_at ke null biar tidak kirim lagi
                $announcement->update(['scheduled_at' => null]);
            }
        }

        $this->info('🎉 Selesai!');
    }
}
