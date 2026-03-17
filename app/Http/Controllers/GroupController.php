<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupBot;
use App\Models\GroupRole;
use App\Models\GroupMember;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GroupController extends Controller
{
    public function index()
    {
        $groups = auth()->user()->groups()->withPivot('role')->get();
        return view('groups.index', compact('groups'));
    }
    public function store(Request $request)
    {
        if (!auth()->user()->isSubscribed()) {
            return redirect()->back()->with('error', 'Anda harus subscribe terlebih dahulu');
        }

        $subscription = auth()->user()->activeSubscription()->with('plan')->first();
        $plan         = $subscription->plan;
        $groupCount   = auth()->user()->groupMembers()
            ->whereHas('role', fn($q) => $q->where('is_owner', true))
            ->count();

        if ($groupCount >= $plan->max_group) {
            return redirect()->back()->with('error', "Maksimal {$plan->max_group} group untuk plan {$plan->name}");
        }

        $group = Group::create([
            'name' => $request->name,
            'owner_id' => auth()->id(),
            'invitation_code_pj' => Str::random(8),
            'invitation_code_member' => Str::random(8),
        ]);

        // Buat 3 default role
        $ownerRole = GroupRole::create([
            'group_id' => $group->id,
            'name' => 'Komti',
            'color' => '#0d6efd',
            'can_create_announcement' => true,
            'can_edit_announcement' => true,
            'can_manage_member'  => true,
            'can_generate_code' => true,
            'can_manage_bot' => true,
            'is_owner'  => true,
        ]);

        GroupRole::create([
            'group_id' => $group->id,
            'name' => 'PJ',
            'color' => '#198754',
            'can_create_announcement' => true,
            'can_edit_announcement' => true,
            'can_manage_member'  => false,
            'can_generate_code' => false,
            'can_manage_bot' => false,
            'is_owner'  => false,
        ]);

        GroupRole::create([
            'group_id' => $group->id,
            'name'  => 'Member',
            'color' => '#6c757d',
            'can_create_announcement' => false,
            'can_edit_announcement' => false,
            'can_manage_member' => false,
            'can_generate_code' => false,
            'can_manage_bot' => false,
            'is_owner' => false,
        ]);

        // Owner otomatis dapat role owner
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
            'role_id' => $ownerRole->id,
        ]);

        // Generate bot sesuai plan
        if ($plan->whatsapp) {
            GroupBot::create(['group_id' => $group->id, 'type' => 'whatsapp', 'invitation_code' => Str::random(10), 'is_active' => true]);
        }
        if ($plan->discord) {
            GroupBot::create(['group_id' => $group->id, 'type' => 'discord', 'invitation_code' => Str::random(10), 'is_active' => true]);
        }
        if ($plan->telegram) {
            GroupBot::create(['group_id' => $group->id, 'type' => 'telegram', 'invitation_code' => Str::random(10), 'is_active' => true]);
        }

        return back()->with('success', 'Group berhasil dibuat!');
    }
    public function updateBotChannel(Request $request, Group $group, GroupBot $bot)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();

        if (!$member || $member->pivot->role !== 'komti') {
            abort(403);
        }

        $bot->update([
            'discord_channel_id' => $request->discord_channel_id,
        ]);

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
        $announcements = $group->announcements()->with('user')->get();

        $discordChannelName = null;
        $telegramGroupName  = null;

        if ($role->can_manage_bot) {
            $notification = new \App\Services\NotificationService();
            $discordBot   = $group->bots->where('type', 'discord')->first();
            $telegramBot  = $group->bots->where('type', 'telegram')->first();

            if ($discordBot?->discord_channel_id) {
                $discordChannelName = $notification->getDiscordChannelName($discordBot->discord_channel_id);
            }
            if ($telegramBot?->telegram_chat_id) {
                $telegramGroupName = $notification->getTelegramChatName($telegramBot->telegram_chat_id);
            }
        }

        return view('pages.group', compact('group', 'role', 'members', 'roles', 'announcements', 'discordChannelName', 'telegramGroupName'));
    }
    public function generateCode(Request $request, Group $group)
    {
        // Hanya komti yang boleh generate ulang kode
        $member = $group->members()->where('user_id', auth()->id())->first();

        if (!$member || $member->pivot->role !== 'komti') {
            abort(403);
        }

        $type = $request->type; // 'pj' atau 'member'

        if ($type === 'pj') {
            $group->update(['invitation_code_pj' => Str::random(8)]);
        } elseif ($type === 'member') {
            $group->update(['invitation_code_member' => Str::random(8)]);
        }

        return back()->with('success', 'Kode undangan berhasil diperbarui');
    }

    public function updateTelegramChat(Request $request, Group $group, GroupBot $bot)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();

        if (!$member || $member->pivot->role !== 'komti') {
            abort(403);
        }

        $bot->update([
            'telegram_chat_id' => $request->telegram_chat_id,
        ]);

        return back()->with('success', 'Telegram Chat ID berhasil disimpan.');
    }
    public function fetchTelegramChat(Request $request, Group $group, GroupBot $bot)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();

        if (!$member || $member->pivot->role !== 'komti') {
            abort(403);
        }

        $token    = config('services.telegram.token');
        $response = Http::get("https://api.telegram.org/bot{$token}/getUpdates");

        if (!$response->successful()) {
            return back()->with('error', 'Gagal fetch data dari Telegram.');
        }

        $updates = $response->json('result');

        if (empty($updates)) {
            return back()->with('error', 'Tidak ada update dari Telegram. Pastikan sudah ketik /start di group Telegram kamu.');
        }

        // Ambil chat ID terbaru dari group (bukan private)
        $chatId = null;
        foreach (array_reverse($updates) as $update) {
            $chat = $update['message']['chat'] ?? null;
            if ($chat && in_array($chat['type'], ['group', 'supergroup'])) {
                $chatId = $chat['id'];
                break;
            }
        }

        if (!$chatId) {
            return back()->with('error', 'Tidak ditemukan group Telegram. Pastikan bot sudah di-add ke group dan sudah ketik /start.');
        }

        $bot->update(['telegram_chat_id' => $chatId]);

        return back()->with('success', "Chat ID berhasil didapat: {$chatId}");
    }
}
