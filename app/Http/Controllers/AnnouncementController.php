<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupRole;
use App\Models\GroupMember;
use App\Models\AnnouncementReaction;
use App\Services\ActivityLogService;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\AnnouncementAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnnouncementController extends Controller
{

    public function store(Request $request, Group $group)
    {
        $role = $this->getRole($group);
        if (!$role->can_create_announcement) abort(403);

        $request->validate([
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'scheduled_at'  => 'nullable|date',
            'repeat'        => 'required|in:none,daily,weekly,monthly',
            'attachments'   => 'nullable|array|max:3',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xlsx,xls',
        ]);

        $scheduledAt = $request->filled('scheduled_at')
            ? Carbon::parse($request->scheduled_at)
            : now();

        $announcement = Announcement::create([
            'group_id'         => $group->id,
            'user_id'          => auth()->id(),
            'title'            => $request->title,
            'content'          => $request->content,
            'scheduled_at'     => $scheduledAt,
            'repeat'           => $request->repeat,
            'use_picker'       => $request->boolean('use_picker'),
            'picker_mode'      => $request->picker_mode ?? 'members',
            'pick_count'       => $request->pick_count ?? 1,
            'pick_role_id'     => $request->pick_role_id ?? null,
            'custom_pick_list' => $request->picker_mode === 'custom'
                ? array_filter(explode("\n", $request->custom_pick_list))
                : null,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $this->saveAttachment($file, $announcement);
            }
        }

        ActivityLogService::log(
            groupId: $group->id,
            type: 'create_announcement',
            description: auth()->user()->name . ' membuat announcement "' . $announcement->title . '"',
            meta: ['announcement_id' => $announcement->id, 'title' => $announcement->title]
        );

        return back()->with('success', 'Announcement berhasil dibuat.');
    }

    public function update(Request $request, Group $group, Announcement $announcement)
    {
        $role = $this->getRole($group);
        if (!$role->can_edit_announcement) abort(403);

        $request->validate([
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'scheduled_at'  => 'nullable|date',
            'repeat'        => 'required|in:none,daily,weekly,monthly',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xlsx,xls',
        ]);

        // Cek total attachment tidak lebih dari 3
        $existingCount = $announcement->attachments()->count();
        $newCount      = $request->hasFile('attachments') ? count($request->file('attachments')) : 0;

        if ($existingCount + $newCount > 3) {
            return back()->with('error', 'Maksimal 3 lampiran per announcement!');
        }

        $oldTitle = $announcement->title;

        $scheduledAt = $request->filled('scheduled_at')
            ? Carbon::parse($request->scheduled_at)
            : now();

        $announcement->update([
            'title'            => $request->title,
            'content'          => $request->content,
            'scheduled_at'     => $scheduledAt,
            'repeat'           => $request->repeat,
            'use_picker'       => $request->boolean('use_picker'),
            'picker_mode'      => $request->picker_mode ?? 'members',
            'pick_count'       => $request->pick_count ?? 1,
            'pick_role_id'     => $request->pick_role_id ?? null,
            'custom_pick_list' => $request->picker_mode === 'custom'
                ? array_filter(explode("\n", $request->custom_pick_list))
                : null,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $this->saveAttachment($file, $announcement);
            }
        }

        ActivityLogService::log(
            groupId: $group->id,
            type: 'edit_announcement',
            description: auth()->user()->name . ' mengedit announcement "' . $oldTitle . '"',
            meta: ['announcement_id' => $announcement->id, 'old_title' => $oldTitle, 'new_title' => $request->title]
        );

        return redirect("/groups/{$group->id}")->with('success', 'Announcement berhasil diupdate.');
    }

    private function saveAttachment($file, Announcement $announcement): void
    {
        $mime = $file->getMimeType();
        $type = str_starts_with($mime, 'image/') ? 'image' : 'document';
        $path = $file->store("announcements/{$announcement->group_id}/{$announcement->id}", 'public');

        AnnouncementAttachment::create([
            'announcement_id' => $announcement->id,
            'filename'        => $file->getClientOriginalName(),
            'path'            => $path,
            'type'            => $type,
            'mime_type'       => $mime,
            'size'            => $file->getSize(),
        ]);
    }

    public function deleteAttachment(Group $group, Announcement $announcement, AnnouncementAttachment $attachment)
    {
        $role = $this->getRole($group);
        if (!$role->can_edit_announcement) abort(403);

        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return back()->with('success', 'Lampiran berhasil dihapus.');
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
    public function pin(Request $request, Group $group, Announcement $announcement)
    {
        $role = $this->getRole($group);
        if (!$role->can_edit_announcement) abort(403);

        // Toggle pin
        $announcement->is_pinned = !$announcement->is_pinned;
        $announcement->save();

        return back()->with('success', $announcement->is_pinned ? 'Announcement berhasil dipin!' : 'Announcement berhasil diunpin!');
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

        Log::info('save result', [
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
