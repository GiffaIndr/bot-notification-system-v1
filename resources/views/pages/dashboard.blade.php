@extends('layout.cdn')

@section('content')

    <h3 class="mb-4">Dashboard</h3>

    <div class="row g-4">

        {{-- Subscription Plans --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header fw-bold">📋 Subscription Plan</div>
                <div class="card-body">

                    @if ($subscription)
                        <div class="alert alert-success py-2">
                            ✅ Aktif hingga: <strong>{{ $subscription->expires_at->format('d M Y') }}</strong>
                        </div>
                        <p class="text-muted small">Subscribe lagi untuk perpanjang +6 bulan.</p>
                    @else
                        <div class="alert alert-warning py-2">
                            ⚠️ Kamu belum berlangganan.
                        </div>
                    @endif

                    @foreach ($plans as $plan)
                        <div class="card mb-3 border">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $plan->name }}</h6>
                                    <span class="fw-bold text-primary">Rp {{ number_format($plan->price, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-muted small mt-1 mb-1">{{ $plan->description }}</p>
                                <p class="text-muted small mb-2">
                                    {{ $plan->whatsapp ? '✅' : '❌' }} WhatsApp &nbsp;|&nbsp;
                                    {{ $plan->discord  ? '✅' : '❌' }} Discord &nbsp;|&nbsp;
                                    👥 Max {{ $plan->max_group }} Group
                                </p>
                                <button class="btn btn-sm btn-primary w-100" onclick="pay({{ $plan->id }})">
                                    {{ $subscription ? 'Perpanjang/Upgrade' : 'Subscribe' }}
                                </button>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        {{-- Kolom kanan: Create Group + Join Group --}}
        <div class="col-md-8">
            <div class="row g-4">

                {{-- Create Group --}}
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header fw-bold">➕ Create Group</div>
                        <div class="card-body d-flex flex-column justify-content-center">

                            @if (!$subscription)
                                <div class="text-center py-3">
                                    <span style="font-size: 2.5rem">🔒</span>
                                    <p class="mt-2 text-muted mb-0">Subscribe untuk membuat group.</p>
                                </div>

                            @elseif ($groupCount >= $maxGroup)
                                <div class="text-center py-3">
                                    <span style="font-size: 2.5rem">🚫</span>
                                    <p class="mt-2 mb-0 text-muted">Batas group tercapai.</p>
                                    <p class="text-muted small mb-0">
                                        {{ $groupCount }}/{{ $maxGroup }} — Upgrade plan untuk menambah group.
                                    </p>
                                </div>

                            @else
                                <p class="text-muted small mb-3">
                                    Group: <strong>{{ $groupCount }}/{{ $maxGroup }}</strong>
                                </p>
                                <form method="POST" action="/groups">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="text" name="name" class="form-control" placeholder="Nama Group">
                                    </div>
                                    <button class="btn btn-primary w-100">Create Group</button>
                                </form>
                            @endif

                        </div>
                    </div>
                </div>

                {{-- Join Group --}}
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header fw-bold">🔗 Join Group</div>
                        <div class="card-body d-flex flex-column justify-content-center">
                            <form method="POST" action="/join">
                                @csrf
                                <div class="mb-3">
                                    <input type="text" name="code" class="form-control" placeholder="Invitation Code">
                                </div>
                                <button class="btn btn-warning w-100">Join</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- My Groups --}}
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                            <span>👥 My Groups</span>
                            @if ($totalGroups > 2)
                                <a href="/groups" class="btn btn-sm btn-outline-primary">
                                    Selengkapnya ({{ $totalGroups }})
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @forelse ($groups as $group)
                                    <div class="col-md-4 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <h6>{{ $group->name }}</h6>
                                                <p class="text-muted small mb-2">
                                                    Role:
                                                    @if ($group->pivot->role === 'komti')
                                                        <span class="badge bg-primary">Komti</span>
                                                    @elseif ($group->pivot->role === 'pj')
                                                        <span class="badge bg-success">PJ</span>
                                                    @else
                                                        <span class="badge bg-secondary">Member</span>
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
                                        <p class="text-muted mb-0">Kamu belum bergabung di group manapun.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        function pay(planId) {
            fetch('/payment/snap-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ plan_id: planId })
                })
                .then(res => res.json())
                .then(data => {
                    snap.pay(data.token, {
                        onSuccess: function(result) {
                            fetch('/payment/sync-bots', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ order_id: result.order_id })
                            }).then(() => location.reload());
                        },
                        onPending: function(result) {
                            alert("Waiting Payment");
                        },
                        onError: function(result) {
                            alert("Payment Failed");
                        }
                    });
                });
        }
    </script>

@endsection
