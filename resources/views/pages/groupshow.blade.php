@extends('layout.sidebar')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="/dashboard" class="text-muted text-decoration-none">
                <i class="fa fa-arrow-left me-1"></i> Dashboard
            </a>
            <h3 class="mt-1 mb-0">My Groups</h3>
        </div>
    </div>

    <div class="row">
        @forelse ($groups as $group)
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>{{ $group->name }}</h5>
                        <p class="text-muted small mb-2">
                            Role:
                            @php
                                $memberRole = \App\Models\GroupMember::where('group_id', $group->id)
                                                ->where('user_id', auth()->id())
                                                ->with('role')
                                                ->first();
                            @endphp
                            @if ($memberRole?->role)
                                <span class="badge" style="background-color: {{ $memberRole->role->color }}">
                                    {{ $memberRole->role->name }}
                                </span>
                            @endif
                        </p>
                        <a href="/groups/{{ $group->id }}" class="btn btn-sm btn-outline-primary w-100">
                            Open
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">Kamu belum bergabung di group manapun.</p>
            </div>
        @endforelse
    </div>

@endsection
