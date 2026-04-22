<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupBot;
use App\Models\GroupRole;
use App\Models\GroupMember;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = auth()->user()->groups()->withPivot('role_id')->get();
        return view('pages.groupshow', compact('groups'));
    }
    public function logs(Group $group)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member) abort(403, 'Anda bukan anggota group ini.');

        $logs = $group->activityLogs()->with('user')->paginate(20);

        return view('pages.logs', compact('group', 'logs'));
    }
    public function store(Request $request)
    {
        if (!auth()->user()->isSubscribed()) {
            return redirect()->back()->with('error', 'Anda harus subscribe terlebih dahulu');
        }

        $subscription = auth()->user()->activeSubscription()->first();
        $groupCount   = auth()->user()->groupMembers()
            ->whereHas('role', fn($q) => $q->where('is_owner', true))
            ->count();

        if ($groupCount >= $subscription->max_groups) {
            return redirect()->back()->with('error', "Maksimal {$subscription->max_groups} group untuk langganan kamu");
        }

        $group = Group::create([
            'name'                   => $request->name,
            'owner_id'               => auth()->id(),
            'invitation_code_pj'     => Str::random(8),
            'invitation_code_member' => Str::random(8),
        ]);

        // Buat 3 default role
        $ownerRole = GroupRole::create([
            'group_id'                => $group->id,
            'name'                    => 'Owner',
            'color'                   => '#0d6efd',
            'can_create_announcement' => true,
            'can_edit_announcement'   => true,
            'can_manage_member'       => true,
            'can_generate_code'       => true,
            'can_manage_bot'          => true,
            'can_create_poll'         => true,
            'is_owner'                => true,
        ]);

        GroupRole::create([
            'group_id'                => $group->id,
            'name'                    => 'Editor',
            'color'                   => '#198754',
            'can_create_announcement' => true,
            'can_edit_announcement'   => true,
            'can_manage_member'       => false,
            'can_generate_code'       => false,
            'can_manage_bot'          => false,
            'can_create_poll'         => true,
            'is_owner'                => false,
        ]);

        GroupRole::create([
            'group_id'                => $group->id,
            'name'                    => 'Member',
            'color'                   => '#6c757d',
            'can_create_announcement' => false,
            'can_edit_announcement'   => false,
            'can_manage_member'       => false,
            'can_generate_code'       => false,
            'can_manage_bot'          => false,
            'can_create_poll'         => false,
            'is_owner'                => false,
        ]);

        // Owner otomatis jadi member dengan role owner
        GroupMember::create([
            'group_id' => $group->id,
            'user_id'  => auth()->id(),
            'role_id'  => $ownerRole->id,
        ]);

        // Generate bot sesuai subscription
        if ($subscription->has_whatsapp) {
            GroupBot::create([
                'group_id'        => $group->id,
                'type'            => 'whatsapp',
                'invitation_code' => Str::random(10),
                'is_active'       => true,
            ]);
        }
        if ($subscription->has_discord) {
            GroupBot::create([
                'group_id'        => $group->id,
                'type'            => 'discord',
                'invitation_code' => Str::random(10),
                'is_active'       => true,
            ]);
        }
        if ($subscription->has_telegram) {
            GroupBot::create([
                'group_id'        => $group->id,
                'type'            => 'telegram',
                'invitation_code' => Str::random(10),
                'is_active'       => true,
            ]);
        }

        return back()->with('success', 'Group berhasil dibuat!');
    }
    public function kickMember(Group $group, GroupMember $member)
    {
        $requester = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$requester || !$requester->role->can_manage_member) {
            abort(403);
        }

        // Tidak bisa kick owner
        if ($member->role->is_owner) {
            return back()->with('error', 'Tidak bisa kick owner!');
        }

        // Tidak bisa kick diri sendiri
        if ($member->user_id === auth()->id()) {
            return back()->with('error', 'Tidak bisa kick diri sendiri!');
        }

        $member->delete();

        return back()->with('success', 'Member berhasil dikeluarkan.');
    }
    public function updateBotChannel(Request $request, Group $group, GroupBot $bot)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')->first();

        if (!$member || !$member->role->can_manage_bot) abort(403);

        $bot->update(['discord_channel_id' => $request->discord_channel_id]);

        ActivityLogService::log(
            groupId: $group->id,
            type: 'bot_connected',
            description: auth()->user()->name . ' menghubungkan bot Discord',
            meta: ['bot_type' => 'discord', 'channel_id' => $request->discord_channel_id]
        );

        return back()->with('success', 'Discord channel berhasil disimpan.');
    }
    public function show(Group $group)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member) abort(403, 'Anda bukan anggota group ini.');

        $role    = $member->role;
        $members = GroupMember::where('group_id', $group->id)->with(['user', 'role'])->get();
        $roles   = $group->roles;
        $announcements = $group->announcements()
            ->with(['user', 'reactions', 'attachments'])
            ->orderByRaw('is_pinned DESC')
            ->orderByDesc('created_at')
            ->get();
        
        $announcementsPreview = $announcements->take(2);
        $announcementsMore = $announcements->skip(2);

        $discordChannelName = null;
        $discordInviteUrl    = null;
        $telegramGroupName   = null;

        if ($role->can_manage_bot) {
            $notification = new NotificationService();
            $discordBot   = $group->bots->where('type', 'discord')->first();
            $telegramBot  = $group->bots->where('type', 'telegram')->first();

            if ($discordBot?->discord_channel_id) {
                $discordChannelName = $notification->getDiscordChannelName($discordBot->discord_channel_id);
            }
            $discordInviteUrl = $notification->getDiscordInviteUrl((string) $group->id);
            if ($telegramBot?->telegram_chat_id) {
                $telegramGroupName = $notification->getTelegramChatName($telegramBot->telegram_chat_id) ?: $group->name;
            }
        }
        $polls = $group->polls()->with(['options.votes', 'votes', 'user'])->get();

        return view('pages.group', compact(
            'group',
            'role',
            'members',
            'roles',
            'announcements',
            'announcementsPreview',
            'announcementsMore',
            'discordChannelName',
            'discordInviteUrl',
            'polls',
            'telegramGroupName',
        ));
    }

    public function allAnnouncements(Group $group)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member) abort(403, 'Anda bukan anggota group ini.');

        $role    = $member->role;
        $announcements = $group->announcements()
            ->with(['user', 'reactions', 'attachments'])
            ->orderByRaw('is_pinned DESC')
            ->orderByDesc('created_at')
            ->get();

        return view('pages.announcements', compact('group', 'role', 'announcements'));
    }

    public function generateCode(Request $request, Group $group)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')->first();

        if (!$member || !$member->role->can_generate_code) abort(403);

        $type = $request->type;

        if ($type === 'pj') {
            $group->update(['invitation_code_pj' => Str::random(8)]);
        } elseif ($type === 'member') {
            $group->update(['invitation_code_member' => Str::random(8)]);
        }

        ActivityLogService::log(
            groupId: $group->id,
            type: 'generate_code',
            description: auth()->user()->name . ' generate ulang kode ' . strtoupper($type),
            meta: ['code_type' => $type]
        );

        return back()->with('success', 'Kode berhasil diperbarui.');
    }

    public function updateTelegramChat(Request $request, Group $group, GroupBot $bot)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')->first();

        if (!$member || !$member->role->can_manage_bot) abort(403);

        return back()->with('error', 'Gunakan koneksi Telegram berbasis token, bukan input Chat ID manual.');
    }
    public function beginTelegramConnect(Request $request, Group $group, GroupBot $bot)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member || !$member->role->can_manage_bot) {
            abort(403);
        }

        if ($bot->type !== 'telegram') {
            abort(404);
        }

        $notification = new NotificationService();
        $state = implode(':', [
            'group',
            $group->id,
            'bot',
            $bot->id,
            'user',
            auth()->id(),
        ]);

        $result = $notification->requestTelegramConnectLink($state);
        $payload = $result['data'] ?? [];
        $token = data_get($payload, 'data.token') ?? data_get($payload, 'token');
        $connectLink = data_get($payload, 'connect_url') ?? data_get($payload, 'url');

        if (!$result['ok'] || empty($token) || empty($connectLink)) {
            return back()->with('error', data_get($payload, 'message', 'Bot service belum mengembalikan link koneksi Telegram.'));
        }

        $bot->forceFill([
            'telegram_connect_token' => $token,
            'telegram_connect_token_generated_at' => now(),
        ])->save();

        ActivityLogService::log(
            groupId: $group->id,
            type: 'bot_connection_requested',
            description: auth()->user()->name . ' menyiapkan koneksi Telegram',
            meta: ['bot_type' => 'telegram', 'token' => $token]
        );

        return back()
            ->with('success', 'Link koneksi Telegram siap. Buka link di bawah lalu tambahkan bot ke grup target.')
            ->with('telegram_connect_link', $connectLink)
            ->with('telegram_connect_bot_id', $bot->id);
    }

    public function pollTelegramConnectClaim(Request $request, Group $group, GroupBot $bot)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member || !$member->role->can_manage_bot) {
            abort(403);
        }

        if ($bot->type !== 'telegram') {
            abort(404);
        }

        if (empty($bot->telegram_connect_token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token koneksi tidak ada. Buat link koneksi baru.',
            ], 400);
        }

        $notification = new NotificationService();
        $result = $notification->claimTelegramConnect($bot->telegram_connect_token);
        $payload = $result['data'] ?? [];
        $status = (int) ($result['status'] ?? 500);

        if ($status === 202) {
            return response()->json([
                'success' => false,
                'pending' => true,
                'message' => data_get($payload, 'message', 'Belum ada group yang claim token ini.'),
            ], 202);
        }

        if ($status >= 400) {
            if (in_array($status, [404, 410], true)) {
                $bot->forceFill([
                    'telegram_connect_token' => null,
                    'telegram_connect_token_generated_at' => null,
                ])->save();
            }

            return response()->json([
                'success' => false,
                'message' => data_get($payload, 'message', 'Gagal claim koneksi Telegram.'),
            ], $status);
        }

        $chatId = data_get($payload, 'data.chat.id')
            ?? data_get($payload, 'chat.id')
            ?? data_get($payload, 'chat_id');
        $chatName = data_get($payload, 'data.chat.title')
            ?? data_get($payload, 'chat.title')
            ?? data_get($payload, 'chat_title');

        if (empty($chatId)) {
            return response()->json([
                'success' => false,
                'message' => 'Bot service belum mengembalikan chat id hasil claim.',
            ], 422);
        }

        $bot->forceFill([
            'telegram_chat_id' => (string) $chatId,
            'telegram_connect_token' => null,
            'telegram_connect_token_generated_at' => null,
        ])->save();

        ActivityLogService::log(
            groupId: $group->id,
            type: 'bot_connected',
            description: auth()->user()->name . ' menghubungkan bot Telegram',
            meta: [
                'bot_type' => 'telegram',
                'chat_id' => (string) $chatId,
                'chat_name' => $chatName,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => data_get($payload, 'message', 'Telegram group berhasil terhubung.'),
            'data' => [
                'chat_id' => (string) $chatId,
                'chat_name' => $chatName,
            ],
        ]);
    }

    public function fetchTelegramChat(Request $request, Group $group, GroupBot $bot)
    {
        return $this->beginTelegramConnect($request, $group, $bot);
    }
    // GroupController.php
    public function picker(Request $request, Group $group)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->first();
        if (!$member) abort(403);

        $count = $request->count ?? 1;

        if ($request->mode === 'custom') {
            $list   = collect($request->custom_list)->filter()->values()->shuffle();
            $picked = $list->take($count);
        } else {
            $query = $group->members();
            if ($request->role_id) {
                $query->wherePivot('role_id', $request->role_id);
            }
            $members = $query->get()->shuffle();
            $picked  = $members->take($count)->pluck('name');
        }

        return response()->json([
            'picked' => $picked->values()->toArray(),
        ]);
    }
    public function update(Request $request, Group $group)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member || !$member->role->is_owner) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->update(['name' => $request->name]);

        return back()->with('success', 'Nama group berhasil diupdate!');
    }
}
