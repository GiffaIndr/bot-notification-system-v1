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

        // Cek apakah kode cocok untuk PJ
        $group = Group::where('invitation_code_pj', $code)->first();
        if ($group) {
            $group->members()->attach(auth()->id(), ['role' => 'pj']);
            return back()->with('success', 'Berhasil join sebagai PJ');
        }

        // Cek apakah kode cocok untuk Member
        $group = Group::where('invitation_code_member', $code)->first();
        if ($group) {
            $group->members()->attach(auth()->id(), ['role' => 'member']);
            return back()->with('success', 'Berhasil join sebagai Member');
        }

        return back()->with('error', 'Kode undangan tidak valid');
    }
}
