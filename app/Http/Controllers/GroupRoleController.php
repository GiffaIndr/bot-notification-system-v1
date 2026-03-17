<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupRole;
use App\Models\GroupMember;
use Illuminate\Http\Request;

class GroupRoleController extends Controller
{
    // Buat role baru
    public function store(Request $request, Group $group)
    {
        $this->checkPermission($group, 'can_manage_member');

        $request->validate([
            'name'  => 'required|string|max:50',
            'color' => 'required|string',
        ]);

        GroupRole::create([
            'group_id'                => $group->id,
            'name'                    => $request->name,
            'color'                   => $request->color,
            'can_create_announcement' => $request->boolean('can_create_announcement'),
            'can_edit_announcement'   => $request->boolean('can_edit_announcement'),
            'can_manage_member'       => $request->boolean('can_manage_member'),
            'can_generate_code'       => $request->boolean('can_generate_code'),
            'can_manage_bot'          => $request->boolean('can_manage_bot'),
            'is_owner'                => false,
        ]);

        return back()->with('success', 'Role berhasil dibuat!');
    }

    // Update role
    public function update(Request $request, Group $group, GroupRole $role)
    {
        $this->checkPermission($group, 'can_manage_member');

        $request->validate([
            'name'  => 'required|string|max:50',
            'color' => 'required|string',
        ]);

        $role->update([
            'name'                    => $request->name,
            'color'                   => $request->color,
            'can_create_announcement' => $request->boolean('can_create_announcement'),
            'can_edit_announcement'   => $request->boolean('can_edit_announcement'),
            'can_manage_member'       => $request->boolean('can_manage_member'),
            'can_generate_code'       => $request->boolean('can_generate_code'),
            'can_manage_bot'          => $request->boolean('can_manage_bot'),
        ]);

        return back()->with('success', 'Role berhasil diupdate!');
    }

    // Hapus role
    public function destroy(Group $group, GroupRole $role)
    {
        $this->checkPermission($group, 'can_manage_member');

        if ($role->is_owner) {
            return back()->with('error', 'Role owner tidak bisa dihapus!');
        }

        $role->delete();
        return back()->with('success', 'Role berhasil dihapus!');
    }

    // Assign role ke member
    public function assignRole(Request $request, Group $group)
    {
        $this->checkPermission($group, 'can_manage_member');

        GroupMember::where('group_id', $group->id)
            ->where('user_id', $request->user_id)
            ->update(['role_id' => $request->role_id]);

        return back()->with('success', 'Role member berhasil diupdate!');
    }

    private function checkPermission(Group $group, string $permission)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member || !$member->role->$permission) {
            abort(403);
        }
    }
}
