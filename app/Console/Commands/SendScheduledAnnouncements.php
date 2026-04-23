<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class SendScheduledAnnouncements extends Command
{
    protected $signature = 'announcements:send';
    protected $description = 'Kirim pengumuman terjadwal ke Telegram/Discord via bot service';

    public function handle()
    {
        $notification = new NotificationService();

        $now = Carbon::now();

        $announcements = Announcement::with(['group.bots', 'category'])
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereIn('status', ['pending', 'failed']);
            })
            ->get();

        $hasReminderColumns = Schema::hasColumns('announcements', [
            'reminder_enabled',
            'reminder_at',
            'reminder_sent_at',
            'reminder_send_status',
        ]);

        $reminderAnnouncements = collect();
        if ($hasReminderColumns) {
            $reminderAnnouncements = Announcement::with(['group.bots', 'category'])
                ->where('reminder_enabled', true)
                ->whereNotNull('reminder_at')
                ->where('reminder_at', '<=', $now)
                ->whereNull('reminder_sent_at')
                ->get();
        }

        if ($announcements->isEmpty() && $reminderAnnouncements->isEmpty()) {
            $this->info('Tidak ada announcement yang perlu dikirim.');
            return;
        }

        foreach ($announcements as $announcement) {
            $message = $this->buildMessage($announcement);

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

        foreach ($reminderAnnouncements as $announcement) {
            $message = $this->buildMessage(
                $announcement,
                true,
                $this->buildReminderPrefix($announcement)
            );

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

                if (!$telegramOk) {
                    $statusCode = $telegramResponse['status'] ?? 500;
                    $warnings[] = "Pengingat Telegram gagal (HTTP {$statusCode}).";
                }
            }

            if ($discordBot?->discord_channel_id) {
                $hasTarget = true;
                $discordOk = $notification->sendDiscord($discordBot->discord_channel_id, $message);

                if (!$discordOk) {
                    $warnings[] = "Pengingat Discord gagal mengirim ke channel {$discordBot->discord_channel_id}.";
                }
            }

            if (!$hasTarget) {
                $announcement->update(['reminder_send_status' => 'failed']);
                $this->warn("⚠️ Pengingat pengumuman #{$announcement->id} gagal: target Telegram/Discord belum diset.");
                continue;
            }

            $allSucceeded = (!is_null($telegramOk) ? $telegramOk : true) && (!is_null($discordOk) ? $discordOk : true);

            if ($allSucceeded) {
                $announcement->update([
                    'reminder_send_status' => 'sent',
                    'reminder_sent_at' => $now,
                ]);
                $this->info("✅ Pengingat pengumuman #{$announcement->id} terkirim.");
                continue;
            }

            $announcement->update(['reminder_send_status' => 'failed']);
            foreach ($warnings as $warning) {
                $this->warn("❌ Pengingat pengumuman #{$announcement->id}: {$warning}");
            }
        }

        $this->info('🎉 Selesai!');
    }

    private function buildMessage(Announcement $announcement, bool $isReminder = false, ?string $prefix = null): string
    {
        $title = trim((string) $announcement->title);
        $badge = $isReminder ? '🔔' : '🔊';
        $headline = $isReminder && !empty($prefix)
            ? "{$badge} *{$prefix} - {$title}* {$badge}"
            : "{$badge} *{$title}* {$badge}";

        $metaLines = [];
        if (!empty($announcement->category?->name)) {
            $metaLines[] = '🏷️ Kategori: ' . $announcement->category->name;
        }

        if ($announcement->deadline_mode && $announcement->deadline_at) {
            $metaLines[] = '⏳ Tenggat: ' . $announcement->deadline_at->format('d M Y, H:i');
        }

        $message = $headline;
        if (!empty($metaLines)) {
            $message .= "\n" . implode("\n", $metaLines);
        }

        $message .= "\n\n📝 {$announcement->content}";

        return $message;
    }

    private function buildReminderPrefix(Announcement $announcement): string
    {
        $offsetValue = (int) ($announcement->reminder_offset_value ?? 0);
        $offsetUnitRaw = (string) ($announcement->reminder_offset_unit ?? '');
        $offsetUnitText = $offsetUnitRaw === 'hour' ? 'jam' : 'hari';

        if ($offsetValue <= 0) {
            return 'Pengingat sebelum tenggat waktu';
        }

        return "Pengingat {$offsetValue} {$offsetUnitText} sebelum tenggat waktu";
    }
}
