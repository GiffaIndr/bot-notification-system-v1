<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\GroupMember;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function store(Request $request, Group $group)
    {
        //  dd($request->all());
        $role = $this->getRole($group);
        if (!$role->can_create_poll) abort(403);

        $request->validate([
            'question'     => 'required|string|max:255',
            'type'         => 'required|in:yes_no,multiple_choice',
            'is_anonymous' => 'boolean',
            'closes_at'    => 'nullable|date|after:now',
            'options'      => 'required_if:type,multiple_choice|array|min:2',
            'options.*'    => 'required|string|max:100',
        ]);

        $poll = Poll::create([
            'group_id'     => $group->id,
            'user_id'      => auth()->id(),
            'question'     => $request->question,
            'type'         => $request->type,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'closes_at'    => $request->closes_at,
        ]);

        if ($request->type === 'yes_no') {
            PollOption::create(['poll_id' => $poll->id, 'label' => 'Ya']);
            PollOption::create(['poll_id' => $poll->id, 'label' => 'Tidak']);
        } else {
            foreach ($request->options as $option) {
                PollOption::create(['poll_id' => $poll->id, 'label' => $option]);
            }
        }

        return back()->with('success', 'Poll berhasil dibuat!');
    }

    public function vote(Request $request, Group $group, Poll $poll)
    {
        // Pastikan member group
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->first();
        if (!$member) abort(403);

        // Cek poll sudah closed
        if ($poll->is_closed || $poll->isExpired()) {
            return back()->with('error', 'Poll sudah ditutup!');
        }

        // Cek sudah vote belum
        $existingVote = PollVote::where('poll_id', $poll->id)
            ->where('user_id', auth()->id())
            ->first();
        if ($existingVote) {
            return back()->with('error', 'Kamu sudah vote!');
        }

        $request->validate([
            'option_id' => 'required|exists:poll_options,id',
        ]);

        PollVote::create([
            'poll_id'        => $poll->id,
            'poll_option_id' => $request->option_id,
            'user_id'        => auth()->id(),
        ]);

        return back()->with('success', 'Vote berhasil!');
    }

    public function close(Group $group, Poll $poll)
    {
        $role = $this->getRole($group);
        if (!$role->can_create_poll) abort(403);

        $poll->update(['is_closed' => true]);
        return back()->with('success', 'Poll berhasil ditutup!');
    }

    public function destroy(Group $group, Poll $poll)
    {
        $role = $this->getRole($group);
        if (!$role->can_create_poll) abort(403);

        $poll->delete();
        return back()->with('success', 'Poll berhasil dihapus!');
    }

    private function getRole(Group $group)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member) abort(403);
        return $member->role;
    }
}
