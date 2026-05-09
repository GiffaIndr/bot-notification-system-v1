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
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{

    public function store(Request $request, Group $group)
    {
        $role = $this->getRole($group);
        if (!$role->can_create_announcement) abort(403);

        $request->validate([
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'category_id'   => [
                'nullable',
                Rule::exists('group_announcement_categories', 'id')->where(
                    fn($query) => $query->where('group_id', $group->id)
                ),
            ],
            'scheduled_at'  => 'required|date',
            'repeat'        => 'required|in:none,daily,weekly,monthly',
            'deadline_mode' => 'nullable|boolean',
            'deadline_at' => 'nullable|required_if:deadline_mode,1|date|after:now',
            'reminder_enabled' => 'nullable|boolean',
            'reminder_offset_value' => 'nullable|required_if:reminder_enabled,1|integer|min:1|max:365',
            'reminder_offset_unit' => 'nullable|required_if:reminder_enabled,1|in:hour,day',
        ]);

        if ($request->repeat !== 'none' && $request->boolean('deadline_mode')) {
            return back()->withErrors([
                'deadline_mode' => 'Mode tenggat hanya bisa dipakai untuk pengumuman yang tidak berulang.',
            ])->withInput();
        }

        $scheduledAt = Carbon::parse($request->scheduled_at);

        $deadlineMode = $request->boolean('deadline_mode');
        $deadlineAt = $deadlineMode && $request->filled('deadline_at')
            ? Carbon::parse($request->deadline_at)
            : null;

        $reminderEnabled = $deadlineMode && $request->boolean('reminder_enabled');
        $reminderOffsetValue = $reminderEnabled ? (int) $request->reminder_offset_value : null;
        $reminderOffsetUnit = $reminderEnabled ? $request->reminder_offset_unit : null;

        $reminderAt = null;
        if ($reminderEnabled && $deadlineAt) {
            $reminderAt = $this->calculateReminderAt($deadlineAt, $reminderOffsetValue, $reminderOffsetUnit);
            if ($reminderAt->lessThanOrEqualTo(now())) {
                return back()->withErrors([
                    'reminder_offset_value' => 'Waktu reminder harus menghasilkan jadwal di masa depan.',
                ])->withInput();
            }
        }

        $announcement = Announcement::create([
            'group_id'         => $group->id,
            'category_id'      => $request->category_id,
            'user_id'          => auth()->id(),
            'title'            => $request->title,
            'content'          => $request->content,
            'scheduled_at'     => $scheduledAt,
            'deadline_mode'    => $deadlineMode,
            'deadline_at'      => $deadlineAt,
            'reminder_enabled' => $reminderEnabled,
            'reminder_offset_value' => $reminderOffsetValue,
            'reminder_offset_unit' => $reminderOffsetUnit,
            'reminder_at'      => $reminderAt,
            'reminder_sent_at' => null,
            'reminder_send_status' => $reminderEnabled ? 'pending' : null,
            'status'           => 'pending',
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

        return back()->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function update(Request $request, Group $group, Announcement $announcement)
    {
        $role = $this->getRole($group);
        if (!$role->can_edit_announcement) abort(403);

        $request->validate([
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'category_id'   => [
                'nullable',
                Rule::exists('group_announcement_categories', 'id')->where(
                    fn($query) => $query->where('group_id', $group->id)
                ),
            ],
            'scheduled_at'  => 'required|date',
            'repeat'        => 'required|in:none,daily,weekly,monthly',
            'deadline_mode' => 'nullable|boolean',
            'deadline_at' => 'nullable|required_if:deadline_mode,1|date|after:now',
            'reminder_enabled' => 'nullable|boolean',
            'reminder_offset_value' => 'nullable|required_if:reminder_enabled,1|integer|min:1|max:365',
            'reminder_offset_unit' => 'nullable|required_if:reminder_enabled,1|in:hour,day',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xlsx,xls',
        ]);

        if ($request->repeat !== 'none' && $request->boolean('deadline_mode')) {
            return back()->withErrors([
                'deadline_mode' => 'Mode tenggat hanya bisa dipakai untuk pengumuman yang tidak berulang.',
            ])->withInput();
        }

        // Cek total attachment tidak lebih dari 3
        $existingCount = $announcement->attachments()->count();
        $newCount      = $request->hasFile('attachments') ? count($request->file('attachments')) : 0;

        if ($existingCount + $newCount > 3) {
            return back()->with('error', 'Maksimal 3 lampiran per announcement!');
        }

        $oldTitle = $announcement->title;

        $scheduledAt = Carbon::parse($request->scheduled_at);

        $deadlineMode = $request->boolean('deadline_mode');
        $deadlineAt = $deadlineMode && $request->filled('deadline_at')
            ? Carbon::parse($request->deadline_at)
            : null;

        $reminderEnabled = $deadlineMode && $request->boolean('reminder_enabled');
        $reminderOffsetValue = $reminderEnabled ? (int) $request->reminder_offset_value : null;
        $reminderOffsetUnit = $reminderEnabled ? $request->reminder_offset_unit : null;

        $reminderAt = null;
        if ($reminderEnabled && $deadlineAt) {
            $reminderAt = $this->calculateReminderAt($deadlineAt, $reminderOffsetValue, $reminderOffsetUnit);
            if ($reminderAt->lessThanOrEqualTo(now())) {
                return back()->withErrors([
                    'reminder_offset_value' => 'Waktu reminder harus menghasilkan jadwal di masa depan.',
                ])->withInput();
            }
        }

        $announcement->update([
            'category_id'      => $request->category_id,
            'title'            => $request->title,
            'content'          => $request->content,
            'scheduled_at'     => $scheduledAt,
            'deadline_mode'    => $deadlineMode,
            'deadline_at'      => $deadlineAt,
            'reminder_enabled' => $reminderEnabled,
            'reminder_offset_value' => $reminderOffsetValue,
            'reminder_offset_unit' => $reminderOffsetUnit,
            'reminder_at'      => $reminderAt,
            'reminder_sent_at' => $reminderEnabled ? null : $announcement->reminder_sent_at,
            'reminder_send_status' => $reminderEnabled ? 'pending' : null,
            'status'           => 'pending',
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

        return redirect("/groups/{$group->id}")->with('success', 'Pengumuman berhasil diperbarui.');
    }

    private function calculateReminderAt(Carbon $deadlineAt, int $offsetValue, string $offsetUnit): Carbon
    {
        return $offsetUnit === 'day'
            ? $deadlineAt->copy()->subDays($offsetValue)
            : $deadlineAt->copy()->subHours($offsetValue);
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

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
    public function pin(Request $request, Group $group, Announcement $announcement)
    {
        $role = $this->getRole($group);
        if (!$role->can_edit_announcement) abort(403);

        // Toggle pin
        $announcement->is_pinned = !$announcement->is_pinned;
        $announcement->save();

        return back()->with('success', $announcement->is_pinned ? 'Pengumuman berhasil disematkan!' : 'Pengumuman berhasil dilepas dari sematan!');
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

    public function updateCategory(Request $request, Group $group, Announcement $announcement)
    {
        $role = $this->getRole($group);
        if (!$role->can_edit_announcement) abort(403);

        $request->validate([
            'category_id' => [
                'nullable',
                Rule::exists('group_announcement_categories', 'id')->where(
                    fn($query) => $query->where('group_id', $group->id)
                ),
            ],
        ]);

        $announcement->update([
            'category_id' => $request->category_id,
        ]);

        return back()->with('success', 'Kategori pengumuman berhasil diperbarui.');
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
