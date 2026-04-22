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
            $message = "🔊 *{$announcement->title}* 🔊\n\n{$announcement->content}";

            $telegramBot = $announcement->group->bots->where('type', 'telegram')->first();
            $discordBot  = $announcement->group->bots->where('type', 'discord')->first();

            $hasTarget = false;
            $telegramOk = null;
            $discordOk = null;
            $warnings = [];

            if ($telegramBot?->telegram_chat_id) {
                $hasTarget = true;
                $telegramResponse = $notification->sendTelegram($telegramBot->telegram_chat_id, $message);
                $telegramOk = $telegramResponse['ok'] === true;

                if ($telegramOk) {
                    $this->info("✅ Announcement #{$announcement->id} terkirim ke Telegram.");
                } else {
                    $statusCode = $telegramResponse['status'] ?? 500;
                    $warnings[] = "Telegram gagal (HTTP {$statusCode}).";
                }
            }

            if ($discordBot?->discord_channel_id) {
                $hasTarget = true;
                $discordOk = $notification->sendDiscord($discordBot->discord_channel_id, $message);

                if ($discordOk) {
                    $this->info("✅ Announcement #{$announcement->id} terkirim ke Discord.");
                } else {
                    $warnings[] = "Discord gagal mengirim ke channel {$discordBot->discord_channel_id}.";
                }
            }

            if (!$hasTarget) {
                $announcement->update(['status' => 'failed']);
                $this->warn("⚠️ Announcement #{$announcement->id} gagal: target Telegram/Discord belum diset.");
                continue;
            }

            $allSucceeded = (!is_null($telegramOk) ? $telegramOk : true) && (!is_null($discordOk) ? $discordOk : true);

            if ($allSucceeded) {
                $announcement->update(['status' => 'sent']);
                continue;
            }

            $announcement->update(['status' => 'failed']);
            foreach ($warnings as $warning) {
                $this->warn("❌ Announcement #{$announcement->id}: {$warning}");
            }
        }

        $this->info('🎉 Selesai!');
    }
}
