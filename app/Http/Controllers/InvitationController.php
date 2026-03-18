<?php

namespace App\Http\Controllers;

use App\Models\InvitationCode;
use App\Models\GroupMember;
use App\Models\Group;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function generate($groupId, $role)
    {
        $invite = InvitationCode::create([
            'group_id' => $groupId,
            'role' => $role,
            'code' => Str::upper(Str::random(6))
        ]);

        return response()->json($invite);
    }

    public function join(Request $request)
    {
        $code = $request->code;

        // Kode PJ/Editor
        $group = Group::where('invitation_code_pj', $code)->first();
        if ($group) {
            $pjRole = $group->roles()
                ->where('is_owner', false)
                ->orderBy('id', 'asc')
                ->first();

            if (!$pjRole) {
                return back()->with('error', 'Role tidak ditemukan di group ini.');
            }

            GroupMember::create([
                'group_id' => $group->id,
                'user_id'  => auth()->id(),
                'role_id'  => $pjRole->id,
            ]);

            return back()->with('success', 'Berhasil join sebagai ' . $pjRole->name);
        }

        // Kode Member
        $group = Group::where('invitation_code_member', $code)->first();
        if ($group) {
            $memberRole = $group->roles()
                ->where('is_owner', false)
                ->orderBy('id', 'asc')
                ->skip(1)
                ->first();

            if (!$memberRole) {
                $memberRole = $group->roles()->where('is_owner', false)->first();
            }

            if (!$memberRole) {
                return back()->with('error', 'Role tidak ditemukan di group ini.');
            }

            GroupMember::create([
                'group_id' => $group->id,
                'user_id'  => auth()->id(),
                'role_id'  => $memberRole->id,
            ]);

            return back()->with('success', 'Berhasil join sebagai ' . $memberRole->name);
        }

        return back()->with('error', 'Kode undangan tidak valid');
    }
}
