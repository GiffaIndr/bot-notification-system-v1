<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupRole;
use App\Models\GroupMember;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    // Hanya komti & pj yang bisa create
    public function store(Request $request, Group $group)
    {
        $role = $this->getRole($group);
        if (!$role->can_create_announcement) abort(403);

        if (!in_array($role, ['komti', 'pj'])) {
            abort(403, 'Anda tidak punya akses.');
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'repeat' => 'required|in:none,daily,weekly,monthly',
        ]);

        Announcement::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
            'title'  => $request->title,
            'content' => $request->content,
            'scheduled_at' => $request->scheduled_at,
            'repeat' => $request->repeat,
        ]);

        return back()->with('success', 'Announcement berhasil dibuat.');
    }

    // Hanya komti & pj yang bisa edit
    public function edit(Group $group, Announcement $announcement)
    {
        $role = $this->getRole($group);

        if (!in_array($role, ['komti', 'pj'])) {
            abort(403, 'Anda tidak punya akses.');
        }

        return view('announcements.edit', compact('group', 'announcement'));
    }

    // Hanya komti & pj yang bisa update
    public function update(Request $request, Group $group, Announcement $announcement)
    {
        $role = $this->getRole($group);
        if (!$role->can_edit_announcement) abort(403);

        if (!in_array($role, ['komti', 'pj'])) {
            abort(403, 'Anda tidak punya akses.');
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'repeat' => 'required|in:none,daily,weekly,monthly',
        ]);

        $announcement->update([
            'title'   => $request->title,
            'content' => $request->content,
            'scheduled_at' => $request->scheduled_at,
            'repeat' => $request->repeat,
        ]);

        return redirect("/groups/{$group->id}")->with('success', 'Announcement berhasil diupdate.');
    }

    // Hanya komti & pj yang bisa delete
    public function destroy(Group $group, Announcement $announcement)
    {
        $role = $this->getRole($group);
        if (!$role->can_edit_announcement) abort(403);

        if (!in_array($role, ['komti', 'pj'])) {
            abort(403, 'Anda tidak punya akses.');
        }

        $announcement->delete();

        return back()->with('success', 'Announcement berhasil dihapus.');
    }

    // Helper ambil role user di group
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
