@extends('layout.cdn')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="/dashboard" class="text-muted text-decoration-none">← Dashboard</a>
            <h3 class="mt-1 mb-0">My Groups</h3>
        </div>
    </div>

    <div class="row">
        @forelse ($groups as $group)
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>{{ $group->name }}</h5>
                        <p class="text-muted">
                            Role:
                            @if ($group->pivot->role === 'komti')
                                <span class="badge bg-primary">Komti</span>
                            @elseif ($group->pivot->role === 'pj')
                                <span class="badge bg-success">PJ</span>
                            @else
                                <span class="badge bg-secondary">Member</span>
                            @endif
                        </p>
                        <a href="/groups/{{ $group->id }}" class="btn btn-sm btn-outline-primary">
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
