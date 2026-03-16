<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupBot;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = auth()->user()->groups()->withPivot('role')->get();
        return view('groups.index', compact('groups'));
    }
    public function store(Request $request)
    {
        if (!auth()->user()->isSubscribed()) {
            return redirect()->back()->with('error', 'Anda harus subscribe terlebih dahulu');
        }

        $subscription = auth()->user()->activeSubscription()->with('plan')->first();
        $plan   = $subscription->plan;

        // Cek jumlah group yang sudah dimiliki
        $groupCount = auth()->user()->groups()
            ->wherePivot('role', 'komti')
            ->count();

        if ($groupCount >= $plan->max_group) {
            return redirect()->back()->with('error', "Anda hanya bisa membuat maksimal {$plan->max_group} group untuk plan {$plan->name}");
        }

        $group = Group::create([
            'name'  => $request->name,
            'owner_id'  => auth()->id(),
            'invitation_code_pj' => Str::random(8),
            'invitation_code_member' => Str::random(8),
        ]);

        $group->members()->attach(auth()->id(), ['role' => 'komti']);

        if ($plan->whatsapp) {
            GroupBot::create([
                'group_id'  => $group->id,
                'type' => 'whatsapp',
                'invitation_code' => Str::random(10),
                'is_active'  => true,
            ]);
        }

        if ($plan->discord) {
            GroupBot::create([
                'group_id' => $group->id,
                'type'  => 'discord',
                'invitation_code' => Str::random(10),
                'is_active'  => true,
            ]);
        }

        return back();
    }
    public function show(Group $group)
    {
        // Pastikan hanya member group yang bisa akses
        $member = $group->members()->where('user_id', auth()->id())->first();

        if (!$member) {
            abort(403, 'Anda bukan anggota group ini');
        }

        $role = $member->pivot->role;
        $members = $group->members()->withPivot('role')->get();
        $announcements = $group->announcements()->with('user')->get();
        return view('pages.group', compact('group', 'role', 'members', 'announcements'));
    }

    public function generateCode(Request $request, Group $group)
    {
        // Hanya komti yang boleh generate ulang kode
        $member = $group->members()->where('user_id', auth()->id())->first();

        if (!$member || $member->pivot->role !== 'komti') {
            abort(403);
        }

        $type = $request->type; // 'pj' atau 'member'

        if ($type === 'pj') {
            $group->update(['invitation_code_pj' => Str::random(8)]);
        } elseif ($type === 'member') {
            $group->update(['invitation_code_member' => Str::random(8)]);
        }

        return back()->with('success', 'Kode undangan berhasil diperbarui');
    }
}
