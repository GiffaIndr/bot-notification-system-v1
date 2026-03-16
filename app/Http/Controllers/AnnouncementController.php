<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    // Hanya komti & pj yang bisa create
    public function store(Request $request, Group $group)
    {
        $role = $this->getRole($group);

        if (!in_array($role, ['komti', 'pj'])) {
            abort(403, 'Anda tidak punya akses.');
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Announcement::create([
            'group_id' => $group->id,
            'user_id'  => auth()->id(),
            'title'    => $request->title,
            'content'  => $request->content,
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

        if (!in_array($role, ['komti', 'pj'])) {
            abort(403, 'Anda tidak punya akses.');
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement->update([
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        return redirect("/groups/{$group->id}")->with('success', 'Announcement berhasil diupdate.');
    }

    // Hanya komti & pj yang bisa delete
    public function destroy(Group $group, Announcement $announcement)
    {
        $role = $this->getRole($group);

        if (!in_array($role, ['komti', 'pj'])) {
            abort(403, 'Anda tidak punya akses.');
        }

        $announcement->delete();

        return back()->with('success', 'Announcement berhasil dihapus.');
    }

    // Helper ambil role user di group
    private function getRole(Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();

        if (!$member) {
            abort(403, 'Anda bukan anggota group ini.');
        }

        return $member->pivot->role;
    }
}
