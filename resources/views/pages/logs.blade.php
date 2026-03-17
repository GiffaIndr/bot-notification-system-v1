@extends('layout.cdn')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="/groups/{{ $group->id }}" class="text-muted text-decoration-none">
                <i class="fa fa-arrow-left me-1"></i> {{ $group->name }}
            </a>
            <h3 class="mt-1 mb-0 fw-bold">Activity Log</h3>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header fw-bold bg-white border-bottom d-flex justify-content-between align-items-center">
            <span><i class="fa fa-clock-rotate-left text-secondary me-2"></i>Semua Aktivitas</span>
            <small class="text-muted">{{ $logs->total() }} aktivitas</small>
        </div>
        <div class="card-body p-0">
            @forelse ($logs as $log)
                <div class="d-flex align-items-start gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">

                    @php
                        $iconClass = match($log->type) {
                            'create_announcement' => 'fa-plus bg-primary',
                            'edit_announcement'   => 'fa-pen bg-warning',
                            'delete_announcement' => 'fa-trash bg-danger',
                            'bot_connected'       => 'fa-robot bg-info',
                            'generate_code'       => 'fa-key bg-warning',
                            'notification_sent'   => 'fa-paper-plane bg-success',
                            default               => 'fa-circle-info bg-secondary',
                        };
                    @endphp

                    {{-- Icon --}}
                    <div class="flex-shrink-0">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white {{ explode(' ', $iconClass)[1] }}"
                              style="width: 36px; height: 36px;">
                            <i class="fa {{ explode(' ', $iconClass)[0] }}"></i>
                        </span>
                    </div>

                    {{-- Isi --}}
                    <div class="flex-grow-1">
                        <p class="mb-0 fw-semibold small">{{ $log->description }}</p>

                        {{-- Meta info --}}
                        @if ($log->meta)
                            <div class="d-flex gap-2 flex-wrap mt-1">
                                @foreach ($log->meta as $key => $value)
                                    <span class="badge bg-light text-dark" style="font-size: 10px">
                                        {{ str_replace('_', ' ', $key) }}: {{ $value }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <small class="text-muted mt-1 d-block">
                            <i class="fa fa-user me-1"></i>{{ $log->user?->name ?? 'System' }}
                            &nbsp;•&nbsp;
                            <i class="fa fa-clock me-1"></i>{{ $log->created_at->format('d M Y, H:i') }}
                            &nbsp;•&nbsp;
                            {{ $log->created_at->diffForHumans() }}
                        </small>
                    </div>

                    {{-- Status --}}
                    <div class="flex-shrink-0">
                        @if ($log->status === 'success')
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="fa fa-circle-check me-1"></i>Sukses
                            </span>
                        @elseif ($log->status === 'failed')
                            <span class="badge bg-danger bg-opacity-10 text-danger">
                                <i class="fa fa-circle-xmark me-1"></i>Gagal
                            </span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                <i class="fa fa-clock me-1"></i>Pending
                            </span>
                        @endif
                    </div>

                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fa fa-clock-rotate-left fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada aktivitas.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($logs->hasPages())
            <div class="card-footer bg-white">
                {{ $logs->links() }}
            </div>
        @endif

    </div>

@endsection
