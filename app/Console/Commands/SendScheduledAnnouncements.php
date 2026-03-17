<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendScheduledAnnouncements extends Command
{
    protected $signature = 'announcements:send';
    protected $description = 'Kirim scheduled announcements via WhatsApp & Discord';

    public function handle()
    {
        $notification = new NotificationService();
        $status       = $notification->isConnected();

        if (!$status['whatsapp'] && !$status['discord']) {
            $this->error('❌ WhatsApp dan Discord service tidak terhubung!');
            return;
        }

        $now = Carbon::now();

        $announcements = Announcement::with(['group.members', 'group.bots'])
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->get();

        if ($announcements->isEmpty()) {
            $this->info('Tidak ada announcement yang perlu dikirim.');
            return;
        }

        foreach ($announcements as $announcement) {

            $message = "📢 *{$announcement->title}*\n\n"
                . "{$announcement->content}\n\n"
                . "🏢 _{$announcement->group->name}_\n"
                . "🕐 _" . $announcement->scheduled_at->format('d M Y, H:i') . "_";

            // Kirim WhatsApp
            if ($status['whatsapp']) {
                $phones = $announcement->group->members()
                    ->whereNotNull('phone')
                    ->pluck('phone')
                    ->toArray();

                if (!empty($phones)) {
                    $notification->sendWhatsapp($phones, $message);
                    $this->info("✅ WA terkirim ke {$announcement->group->name} ({$announcement->group->members()->count()} member)");
                }
            }

            // Kirim Discord
            if ($status['discord']) {
                $discordBot = $announcement->group->bots
                    ->where('type', 'discord')
                    ->first();

                if ($discordBot && $discordBot->discord_channel_id) {
                    $notification->sendDiscord($discordBot->discord_channel_id, $message);
                    $this->info("✅ Discord terkirim ke channel {$discordBot->discord_channel_id}");
                } else {
                    $this->warn("⚠️ Discord channel belum diset untuk group {$announcement->group->name}");
                }
            }

            // Update jadwal berikutnya
            if ($announcement->repeat !== 'none') {
                $next = match ($announcement->repeat) {
                    'daily'   => $announcement->scheduled_at->copy()->addDay(),
                    'weekly'  => $announcement->scheduled_at->copy()->addWeek(),
                    'monthly' => $announcement->scheduled_at->copy()->addMonth(),
                };
                $announcement->update(['scheduled_at' => $next]);
                $this->info("🔁 Jadwal berikutnya: {$next->format('d M Y, H:i')}");
            } else {
                $announcement->update(['scheduled_at' => null]);
            }

            if ($status['whatsapp'] || true) { // telegram tidak perlu cek status koneksi
                $telegramBot = $announcement->group->bots
                    ->where('type', 'telegram')
                    ->first();

                if ($telegramBot && $telegramBot->telegram_chat_id) {
                    $notification->sendTelegram($telegramBot->telegram_chat_id, $message);
                    $this->info("✅ Telegram terkirim ke chat {$telegramBot->telegram_chat_id}");
                } else {
                    $this->warn("⚠️ Telegram chat ID belum diset untuk group {$announcement->group->name}");
                }
            }
        }

        $this->info('🎉 Selesai!');
    }
}
