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

        // Kode Editor
        $group = Group::where('invitation_code_pj', $code)->first();
        if ($group) {
            $editorRole = $group->roles()->where('name', 'Editor')->first();
            GroupMember::create([
                'group_id' => $group->id,
                'user_id'  => auth()->id(),
                'role_id'  => $editorRole->id,
            ]);
            return back()->with('success', 'Berhasil join sebagai ' . $editorRole->name);
        }

        // Kode Member
        $group = Group::where('invitation_code_member', $code)->first();
        if ($group) {
            $memberRole = $group->roles()->where('name', 'Member')->first();
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
