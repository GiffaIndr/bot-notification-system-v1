<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendScheduledAnnouncements extends Command
{
    protected $signature = 'announcements:send';
    protected $description = 'Kirim scheduled announcements ke Telegram via bot-service';

    public function handle()
    {
        $notification = new NotificationService();

        $now = Carbon::now();

        $announcements = Announcement::with(['group.bots'])
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereIn('status', ['pending', 'failed']);
            })
            ->get();

        if ($announcements->isEmpty()) {
            $this->info('Tidak ada announcement yang perlu dikirim.');
            return;
        }

        foreach ($announcements as $announcement) {
            $telegramBot = $announcement->group->bots->where('type', 'telegram')->first();
            if (!$telegramBot || !$telegramBot->telegram_chat_id) {
                $announcement->update(['status' => 'failed']);
                $this->warn("⚠️ Announcement #{$announcement->id} gagal: Telegram chat_id belum tersedia.");
                continue;
            }

            $message = "🔊 *{$announcement->title}* 🔊\n\n{$announcement->content}";
            $response = $notification->sendTelegram($telegramBot->telegram_chat_id, $message);

            if ($response['ok'] === true) {
                $announcement->update(['status' => 'sent']);
                $this->info("✅ Announcement #{$announcement->id} terkirim.");
            } else {
                $announcement->update(['status' => 'failed']);
                $statusCode = $response['status'] ?? 500;
                $this->warn("❌ Announcement #{$announcement->id} gagal (HTTP {$statusCode}).");
            }
        }

        $this->info('🎉 Selesai!');
    }
}
