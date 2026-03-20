<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Models\GroupMember;
use App\Services\NotificationService;
use App\Services\ActivityLogService;
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
            $pickedNames = '';
            if ($announcement->use_picker) {

                if ($announcement->picked_result && count($announcement->picked_result) > 0) {
                    // Pakai hasil undian yang sudah disimpan
                    $pickedNames = "\n\n🎰 *Yang Kena Giliran:*\n"
                        . collect($announcement->picked_result)
                        ->values()
                        ->map(fn($name, $i) => ($i + 1) . ". {$name}")
                        ->join("\n");
                } else {
                    // Belum diundi, pick random saat kirim
                    if ($announcement->picker_mode === 'custom') {
                        $list   = collect($announcement->custom_pick_list)->filter()->values()->shuffle(mt_rand());
                        $picked = $list->take($announcement->pick_count)->values();
                    } else {
                        $members = GroupMember::where('group_id', $announcement->group_id)
                            ->with('user')
                            ->get()
                            ->shuffle(mt_rand());
                        $picked  = $members->take($announcement->pick_count)
                            ->pluck('user.name')
                            ->values();
                    }

                    $pickedNames = "\n\n🎲 *Yang Kena Giliran:*\n"
                        . $picked->map(fn($name, $i) => ($i + 1) . ". {$name}")->join("\n");

                    // Simpan ke DB
                    $announcement->picked_result = $picked->toArray();
                    $announcement->save();
                }
            }

            $reactionSummary = '';
            $reactions = \App\Models\AnnouncementReaction::where('announcement_id', $announcement->id)
                ->selectRaw('emoji, COUNT(*) as count')
                ->groupBy('emoji')
                ->get();

            if ($reactions->isNotEmpty()) {
                $reactionSummary = "\n\n" . $reactions->map(fn($r) => "{$r->emoji} {$r->count}")->join('  ');
            }

            $message = "📢 *{$announcement->title}*\n\n"
                . "{$announcement->content}"
                . $pickedNames
                . "\n\n🏢 _{$announcement->group->name}_\n"
                . "🕐 _" . $announcement->scheduled_at->format('d M Y, H:i') . "_"
                . $reactionSummary;

            // Kirim WhatsApp
            if ($status['whatsapp']) {
                $phones = $announcement->group->members()
                    ->whereNotNull('phone')
                    ->pluck('phone')
                    ->toArray();

                if (!empty($phones)) {
                    $result = $notification->sendWhatsapp($phones, $message);

                    ActivityLogService::log(
                        groupId: $announcement->group_id,
                        type: 'notification_sent',
                        description: 'Notifikasi WhatsApp terkirim untuk announcement "' . $announcement->title . '"',
                        meta: ['bot_type' => 'whatsapp', 'recipients' => count($phones), 'announcement_id' => $announcement->id],
                        status: 'success',
                        userId: null
                    );
                }
            }

            // Kirim Discord
            if ($status['discord']) {
                $discordBot = $announcement->group->bots->where('type', 'discord')->first();

                if ($discordBot && $discordBot->discord_channel_id) {
                    $sent = $notification->sendDiscord($discordBot->discord_channel_id, $message);

                    ActivityLogService::log(
                        groupId: $announcement->group_id,
                        type: 'notification_sent',
                        description: 'Notifikasi Discord ' . ($sent ? 'terkirim' : 'gagal') . ' untuk announcement "' . $announcement->title . '"',
                        meta: ['bot_type' => 'discord', 'channel_id' => $discordBot->discord_channel_id, 'announcement_id' => $announcement->id],
                        status: $sent ? 'success' : 'failed',
                        userId: null
                    );
                }
            }
            $telegramBot = $announcement->group->bots->where('type', 'telegram')->first();
            if ($telegramBot && $telegramBot->telegram_chat_id) {
                $sent = $notification->sendTelegram($telegramBot->telegram_chat_id, $message);

                ActivityLogService::log(
                    groupId: $announcement->group_id,
                    type: 'notification_sent',
                    description: 'Notifikasi Telegram ' . ($sent ? 'terkirim' : 'gagal') . ' untuk announcement "' . $announcement->title . '"',
                    meta: ['bot_type' => 'telegram', 'chat_id' => $telegramBot->telegram_chat_id, 'announcement_id' => $announcement->id],
                    status: $sent ? 'success' : 'failed',
                    userId: null
                );
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


            // if ($status['whatsapp'] || true) { // telegram tidak perlu cek status koneksi
            //     $telegramBot = $announcement->group->bots
            //         ->where('type', 'telegram')
            //         ->first();

            //     if ($telegramBot && $telegramBot->telegram_chat_id) {
            //         $notification->sendTelegram($telegramBot->telegram_chat_id, $message);
            //         $this->info("✅ Telegram terkirim ke chat {$telegramBot->telegram_chat_id}");
            //     } else {
            //         $this->warn("⚠️ Telegram chat ID belum diset untuk group {$announcement->group->name}");
            //     }
            // }
        }
        $this->info('🎉 Selesai!');
    }
}
