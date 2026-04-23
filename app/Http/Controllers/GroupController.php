<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupBot;
use App\Models\GroupRole;
use App\Models\GroupMember;
use App\Models\GroupAnnouncementCategory;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        if ($bot->type !== 'discord') {
            return back()->with('error', 'Bot yang dipilih bukan Discord.');
        }

        return back()->with('error', 'Gunakan koneksi Discord berbasis claim token, bukan input Channel ID manual.');
    }

    public function beginDiscordConnect(Request $request, Group $group, GroupBot $bot)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member || !$member->role->can_manage_bot) {
            abort(403);
        }

        if ($bot->type !== 'discord') {
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

        $result = $notification->createDiscordConnectToken($state);
        $payload = $result['data'] ?? [];
        $token = data_get($payload, 'data.token') ?? data_get($payload, 'token');
        $command = data_get($payload, 'data.command') ?? data_get($payload, 'command');
        $statusText = data_get($payload, 'data.status') ?? data_get($payload, 'status');
        $expiresAt = data_get($payload, 'data.expires_at') ?? data_get($payload, 'expires_at');
        $stateFromService = data_get($payload, 'data.state') ?? data_get($payload, 'state') ?? $state;
        $inviteLink = $notification->getDiscordInviteUrl();

        if (!$result['ok'] || empty($token)) {
            return back()->with('error', data_get($payload, 'message', 'Bot service belum mengembalikan token claim Discord.'));
        }

        if (empty($command)) {
            $command = '/claim token:' . $token;
        }

        $bot->forceFill([
            'discord_connect_token' => $token,
            'discord_connect_state' => $stateFromService,
            'discord_connect_token_generated_at' => now(),
        ])->save();

        ActivityLogService::log(
            groupId: $group->id,
            type: 'bot_connection_requested',
            description: auth()->user()->name . ' menyiapkan koneksi Discord',
            meta: [
                'bot_type' => 'discord',
                'token' => $token,
                'state' => $stateFromService,
            ]
        );

        return back()
            ->with('success', 'Perintah claim Discord siap. Invite bot lalu jalankan command di channel target.')
            ->with('discord_connect_bot_id', $bot->id)
            ->with('discord_connect_command', $command)
            ->with('discord_connect_status', $statusText)
            ->with('discord_connect_expires_at', $expiresAt)
            ->with('discord_invite_link', $inviteLink);
    }

    public function pollDiscordConnectClaim(Request $request, Group $group, GroupBot $bot)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member || !$member->role->can_manage_bot) {
            abort(403);
        }

        if ($bot->type !== 'discord') {
            abort(404);
        }

        if (empty($bot->discord_connect_token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token claim Discord tidak ada. Buat command claim baru.',
            ], 400);
        }

        $notification = new NotificationService();
        $result = $notification->checkDiscordConnectClaim($bot->discord_connect_token);
        $payload = $result['data'] ?? [];
        $status = (int) ($result['status'] ?? 500);

        if ($status === 202) {
            return response()->json([
                'success' => false,
                'pending' => true,
                'message' => data_get($payload, 'message', 'Token belum diklaim di Discord.'),
            ], 202);
        }

        if ($status >= 400) {
            if (in_array($status, [404, 410], true)) {
                $bot->forceFill([
                    'discord_connect_token' => null,
                    'discord_connect_state' => null,
                    'discord_connect_token_generated_at' => null,
                ])->save();
            }

            return response()->json([
                'success' => false,
                'message' => data_get($payload, 'message', 'Gagal claim koneksi Discord.'),
            ], $status);
        }

        $data = data_get($payload, 'data', []);
        $guildId = data_get($data, 'guild.id') ?? data_get($payload, 'guild.id');
        $guildName = data_get($data, 'guild.name') ?? data_get($payload, 'guild.name');
        $channelId = data_get($data, 'channel.id') ?? data_get($payload, 'channel.id');
        $channelName = data_get($data, 'channel.name') ?? data_get($payload, 'channel.name');
        $claimedById = data_get($data, 'claimed_by.id') ?? data_get($payload, 'claimed_by.id');
        $claimedByUsername = data_get($data, 'claimed_by.username') ?? data_get($payload, 'claimed_by.username');

        if (empty($guildId) || empty($channelId)) {
            return response()->json([
                'success' => false,
                'message' => 'Bot service belum mengembalikan guild/channel hasil claim.',
            ], 422);
        }

        $bot->forceFill([
            'discord_guild_id' => (string) $guildId,
            'discord_server_name' => $guildName,
            'discord_channel_id' => (string) $channelId,
            'discord_channel_name' => $channelName,
            'discord_connect_token' => null,
            'discord_connect_state' => null,
            'discord_connect_token_generated_at' => null,
        ])->save();

        ActivityLogService::log(
            groupId: $group->id,
            type: 'bot_connected',
            description: auth()->user()->name . ' menghubungkan bot Discord',
            meta: [
                'bot_type' => 'discord',
                'guild_id' => (string) $guildId,
                'guild_name' => $guildName,
                'channel_id' => (string) $channelId,
                'channel_name' => $channelName,
                'claimed_by_discord_user_id' => $claimedById,
                'claimed_by_discord_username' => $claimedByUsername,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => data_get($payload, 'message', 'Discord channel berhasil terhubung.'),
            'data' => [
                'guild_id' => (string) $guildId,
                'guild_name' => $guildName,
                'channel_id' => (string) $channelId,
                'channel_name' => $channelName,
                'claimed_by_discord_user_id' => $claimedById,
                'claimed_by_discord_username' => $claimedByUsername,
            ],
        ]);
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
        $categories = $group->announcementCategories()->orderBy('name')->get();
        $announcements = $group->announcements()
            ->with(['user', 'reactions', 'attachments', 'category'])
            ->orderByRaw('is_pinned DESC')
            ->orderByDesc('created_at')
            ->get();

        $announcementsPreview = $announcements->take(2);
        $announcementsMore = $announcements->skip(2);

        $discordServerName   = null;
        $discordChannelName  = null;
        $discordGuildId      = null;
        $discordInviteUrl    = null;
        $telegramGroupName   = null;

        if ($role->can_manage_bot) {
            $notification = new NotificationService();
            $discordBot   = $group->bots->where('type', 'discord')->first();
            $telegramBot  = $group->bots->where('type', 'telegram')->first();

            if ($discordBot?->discord_channel_id) {
                $discordGuildId = $discordBot->discord_guild_id;
                $discordServerName = $discordBot->discord_server_name;
                $discordChannelName = $discordBot->discord_channel_name;

                if (!$discordServerName || !$discordChannelName) {
                    $discordInfo = $notification->getDiscordChannelInfo($discordBot->discord_channel_id);
                    $discordData = $discordInfo['data'] ?? [];

                    $discordServerName = $discordServerName ?: ($discordData['server_name'] ?? null);
                    $discordChannelName = $discordChannelName ?: ($discordData['channel_name'] ?? null);
                }
            }
            $discordInviteUrl = $notification->getDiscordInviteUrl();
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
            'categories',
            'announcements',
            'announcementsPreview',
            'announcementsMore',
            'discordGuildId',
            'discordServerName',
            'discordChannelName',
            'discordInviteUrl',
            'polls',
            'telegramGroupName',
        ));
    }

    public function allAnnouncements(Request $request, Group $group)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member) abort(403, 'Anda bukan anggota group ini.');

        $role = $member->role;
        $categories = $group->announcementCategories()->orderBy('name')->get();

        $search = trim((string) $request->query('q', ''));
        $sort = (string) $request->query('sort', 'latest');
        $filter = (string) $request->query('filter', 'all');
        $categoryId = $request->query('category_id');

        $announcementsQuery = $group->announcements()
            ->with(['user', 'reactions', 'attachments', 'category']);

        if ($search !== '') {
            $announcementsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($filter === 'pinned') {
            $announcementsQuery->where('is_pinned', true);
        } elseif ($filter === 'scheduled') {
            $announcementsQuery->whereNotNull('scheduled_at');
        } elseif ($filter === 'repeat') {
            $announcementsQuery->where('repeat', '!=', 'none');
        } elseif ($filter === 'attachment') {
            $announcementsQuery->whereHas('attachments');
        }

        if (!empty($categoryId)) {
            $announcementsQuery->where('category_id', $categoryId);
        }

        if ($sort === 'oldest') {
            $announcementsQuery->orderBy('created_at');
        } elseif ($sort === 'pinned') {
            $announcementsQuery->orderByDesc('is_pinned')->orderByDesc('created_at');
        } else {
            $announcementsQuery->orderByDesc('created_at');
        }

        $announcements = $announcementsQuery->paginate(10)->withQueryString();

        return view('pages.announcements', compact('group', 'role', 'announcements', 'search', 'sort', 'filter', 'categories', 'categoryId'));
    }

    public function storeAnnouncementCategory(Request $request, Group $group)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member || !$member->role->can_edit_announcement) {
            abort(403);
        }

        if ($group->announcementCategories()->count() >= 5) {
            return back()->with('error', 'Maksimal 5 kategori per grup.');
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('group_announcement_categories', 'name')->where(
                    fn($query) => $query->where('group_id', $group->id)
                ),
            ],
        ]);

        $group->announcementCategories()->create([
            'name' => trim($request->name),
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateAnnouncementCategory(Request $request, Group $group, GroupAnnouncementCategory $category)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member || !$member->role->can_edit_announcement) {
            abort(403);
        }

        if ((int) $category->group_id !== (int) $group->id) {
            abort(404);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('group_announcement_categories', 'name')
                    ->where(fn($query) => $query->where('group_id', $group->id))
                    ->ignore($category->id),
            ],
        ]);

        $category->update([
            'name' => trim($request->name),
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyAnnouncementCategory(Group $group, GroupAnnouncementCategory $category)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member || !$member->role->can_edit_announcement) {
            abort(403);
        }

        if ((int) $category->group_id !== (int) $group->id) {
            abort(404);
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
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
