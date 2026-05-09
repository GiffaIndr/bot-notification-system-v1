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
            'can_create_poll'         => $request->boolean('can_create_poll'),
            'can_manage_bot'          => $request->boolean('can_manage_bot'),
            'is_owner'                => false,
        ]);

        return back()->with('success', 'Role berhasil dibuat!');
    }

    // Update role
    public function update(Request $request, Group $group, GroupRole $role)
    {
        $this->checkPermission($group, 'can_manage_member');

        if ((int) $role->group_id !== (int) $group->id) {
            abort(404);
        }

        // Proteksi: owner role tidak boleh diubah permissionnya
        if ($role->is_owner) {
            return back()->with('error', 'Permission role owner tidak bisa diubah!');
        }

        $request->validate([
            'name'  => 'nullable|string|max:50',
            'color' => 'nullable|string',
        ]);

        $role->update([
            'name'                    => $request->filled('name') ? $request->name : $role->name,
            'color'                   => $request->filled('color') ? $request->color : $role->color,
            'can_create_announcement' => $request->boolean('can_create_announcement'),
            'can_edit_announcement'   => $request->boolean('can_edit_announcement'),
            'can_manage_member'       => $request->boolean('can_manage_member'),
            'can_generate_code'       => $request->boolean('can_generate_code'),
            'can_create_poll'         => $role->can_create_poll,
            'can_manage_bot'          => $request->boolean('can_manage_bot'),
        ]);

        return back()->with('success', 'Role berhasil diupdate!');
    }

    // Hapus role
    public function destroy(Group $group, GroupRole $role)
    {
        $this->checkPermission($group, 'can_manage_member');

        if ((int) $role->group_id !== (int) $group->id) {
            abort(404);
        }

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

        // Cek apakah target user adalah owner group
        $targetMember = GroupMember::where('group_id', $group->id)
            ->where('user_id', $request->user_id)
            ->with('role')
            ->first();

        if (!$targetMember) {
            return back()->with('error', 'Member tidak ditemukan.');
        }

        // Proteksi: owner tidak bisa diubah rolenya
        if ($targetMember->role->is_owner) {
            return back()->with('error', 'Role owner tidak bisa diubah!');
        }

        // Proteksi: tidak bisa assign ke role owner
        $targetRole = GroupRole::where('group_id', $group->id)->find($request->role_id);
        if (!$targetRole) {
            return back()->with('error', 'Role tujuan tidak ditemukan.');
        }

        if ($targetRole->is_owner) {
            return back()->with('error', 'Tidak bisa assign role owner ke member lain!');
        }

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
