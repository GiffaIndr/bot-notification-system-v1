<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupRole;
use App\Models\GroupMember;
use App\Models\AnnouncementReaction;
use App\Services\ActivityLogService;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    public function store(Request $request, Group $group)
    {
        $role = $this->getRole($group);

        if (!$role->can_create_announcement) abort(403, 'Anda tidak punya akses.');

        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'scheduled_at' => 'nullable|date',
            'repeat'       => 'required|in:none,daily,weekly,monthly',
        ]);

        $announcement = Announcement::create([
            'group_id'         => $group->id,
            'user_id'          => auth()->id(),
            'title'            => $request->title,
            'content'          => $request->content,
            'scheduled_at'     => $request->scheduled_at,
            'repeat'           => $request->repeat,
            'use_picker'       => $request->boolean('use_picker'),
            'picker_mode'      => $request->picker_mode ?? 'members',
            'pick_count'       => $request->pick_count ?? 1,
            'pick_role_id'     => $request->pick_role_id ?? null,
            'custom_pick_list' => $request->picker_mode === 'custom'
                ? array_filter(explode("\n", $request->custom_pick_list))
                : null,
        ]);

        ActivityLogService::log(
            groupId: $group->id,
            type: 'create_announcement',
            description: auth()->user()->name . ' membuat announcement "' . $announcement->title . '"',
            meta: ['announcement_id' => $announcement->id, 'title' => $announcement->title]
        );

        return back()->with('success', 'Announcement berhasil dibuat.');
    }
    public function react(Request $request, Group $group, Announcement $announcement)
    {
        // Pastikan member group
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->first();
        if (!$member) abort(403);

        $request->validate([
            'emoji' => 'required|in:👍,❤️,😂,😮,😢,😡',
        ]);

        $existing = AnnouncementReaction::where('announcement_id', $announcement->id)
            ->where('user_id', auth()->id())
            ->where('emoji', $request->emoji)
            ->first();

        if ($existing) {
            // Toggle off kalau sudah react dengan emoji yang sama
            $existing->delete();
            $reacted = false;
        } else {
            AnnouncementReaction::create([
                'announcement_id' => $announcement->id,
                'user_id'         => auth()->id(),
                'emoji'           => $request->emoji,
            ]);
            $reacted = true;
        }

        // Return count per emoji
        $reactions = AnnouncementReaction::where('announcement_id', $announcement->id)
            ->selectRaw('emoji, COUNT(*) as count')
            ->groupBy('emoji')
            ->pluck('count', 'emoji');

        return response()->json([
            'reacted'   => $reacted,
            'reactions' => $reactions,
        ]);
    }

    public function update(Request $request, Group $group, Announcement $announcement)
    {
        $role = $this->getRole($group);

        if (!$role->can_edit_announcement) abort(403, 'Anda tidak punya akses.');

        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'scheduled_at' => 'nullable|date',
            'repeat'       => 'required|in:none,daily,weekly,monthly',
        ]);

        $oldTitle = $announcement->title;

        $announcement->update([
            'group_id'         => $group->id,
            'user_id'          => auth()->id(),
            'title'            => $request->title,
            'content'          => $request->content,
            'scheduled_at'     => $request->scheduled_at,
            'repeat'           => $request->repeat,
            'use_picker'       => $request->boolean('use_picker'),
            'picker_mode'      => $request->picker_mode ?? 'members',
            'pick_count'       => $request->pick_count ?? 1,
            'pick_role_id'     => $request->pick_role_id ?? null,
            'custom_pick_list' => $request->picker_mode === 'custom'
                ? array_filter(explode("\n", $request->custom_pick_list))
                : null,
        ]);

        ActivityLogService::log(
            groupId: $group->id,
            type: 'edit_announcement',
            description: auth()->user()->name . ' mengedit announcement "' . $oldTitle . '"',
            meta: ['announcement_id' => $announcement->id, 'old_title' => $oldTitle, 'new_title' => $request->title]
        );

        return redirect("/groups/{$group->id}")->with('success', 'Announcement berhasil diupdate.');
    }

    public function destroy(Group $group, Announcement $announcement)
    {
        $role = $this->getRole($group);

        if (!$role->can_edit_announcement) abort(403, 'Anda tidak punya akses.');

        ActivityLogService::log(
            groupId: $group->id,
            type: 'delete_announcement',
            description: auth()->user()->name . ' menghapus announcement "' . $announcement->title . '"',
            meta: ['announcement_id' => $announcement->id, 'title' => $announcement->title]
        );

        $announcement->delete();

        return back()->with('success', 'Announcement berhasil dihapus.');
    }
    public function previewPick(Request $request, Group $group, Announcement $announcement)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->first();
        if (!$member) abort(403);

        if ($announcement->picker_mode === 'custom') {
            $list   = collect($announcement->custom_pick_list)->filter()->values();
            $picked = $list->shuffle()->take($announcement->pick_count)->values();
        } else {
            $members = GroupMember::where('group_id', $group->id)
                ->with('user')
                ->get()
                ->shuffle()
                ->take($announcement->pick_count)
                ->pluck('user.name')
                ->values();
            $picked = $members;
        }

        $announcement->picked_result = $picked->toArray();
        $result = $announcement->save();

        \Log::info('save result', [
            'saved'          => $result,
            'picked_result'  => $announcement->fresh()->picked_result,
        ]);

        return response()->json([
            'picked' => $picked->toArray(),
        ]);
    }

    private function getRole(Group $group): GroupRole
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member) abort(403, 'Anda bukan anggota group ini.');

        return $member->role;
    }
}
