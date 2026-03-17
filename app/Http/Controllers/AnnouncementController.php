<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupRole;
use App\Models\GroupMember;
use App\Services\ActivityLogService;
use App\Models\Announcement;
use Illuminate\Http\Request;

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
            'group_id'     => $group->id,
            'user_id'      => auth()->id(),
            'title'        => $request->title,
            'content'      => $request->content,
            'scheduled_at' => $request->scheduled_at,
            'repeat'       => $request->repeat,
        ]);

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

        if (!$role->can_edit_announcement) abort(403, 'Anda tidak punya akses.');

        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'scheduled_at' => 'nullable|date',
            'repeat'       => 'required|in:none,daily,weekly,monthly',
        ]);

        $oldTitle = $announcement->title;

        $announcement->update([
            'title'        => $request->title,
            'content'      => $request->content,
            'scheduled_at' => $request->scheduled_at,
            'repeat'       => $request->repeat,
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
