@extends('layout.sidebar')

@section('content')
<style>
    /* Timeline styling */
    .activity-log-container {
        position: relative;
    }

    /* Garis vertikal timeline */
    .activity-log-container::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 48px; /* Sesuaikan dengan posisi ikon */
        width: 2px;
        background: #eef2f7;
    }

    .log-item {
        position: relative;
        z-index: 1;
        transition: all 0.2s ease;
        border-radius: 12px;
        margin-bottom: 5px;
    }

    .log-item:hover {
        background-color: #f8fafc;
        transform: scale(1.01);
    }

    .icon-box {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    /* Soft UI Badges */
    .badge-soft {
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .meta-tag {
        background: #fff;
        border: 1px solid #e2e8f0;
        color: #64748b;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 11px;
    }
</style>
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="/groups" class="text-decoration-none text-muted">Groups</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">{{ $group->name }}</li>
                </ol>
            </nav>
            <h3 class="fw-800 mb-0" style="color: #1e293b;">Activity Log</h3>
        </div>
        <div class="text-end">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                <i class="fa fa-layer-group me-1"></i> {{ $group->name }}
            </span>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark">
                <i class="fa fa-clock-rotate-left text-primary me-2"></i>History Aktivitas
            </span>
            <span class="text-muted small fw-medium">{{ $logs->total() }} record ditemukan</span>
        </div>

        <div class="card-body p-4 activity-log-container">
            @forelse ($logs as $log)
                @php
                    // Map warna dan ikon yang lebih "soft"
                    $config = match($log->type) {
                        'create_announcement' => ['icon' => 'fa-plus', 'color' => '#6366f1', 'bg' => '#eef2ff'],
                        'edit_announcement'   => ['icon' => 'fa-pen', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
                        'delete_announcement' => ['icon' => 'fa-trash', 'color' => '#ef4444', 'bg' => '#fef2f2'],
                        'bot_connected'       => ['icon' => 'fa-robot', 'color' => '#06b6d4', 'bg' => '#ecfeff'],
                        'generate_code'       => ['icon' => 'fa-key', 'color' => '#8b5cf6', 'bg' => '#f5f3ff'],
                        'notification_sent'   => ['icon' => 'fa-paper-plane', 'color' => '#10b981', 'bg' => '#ecfdf5'],
                        default               => ['icon' => 'fa-info-circle', 'color' => '#64748b', 'bg' => '#f8fafc'],
                    };
                @endphp

                <div class="log-item d-flex gap-4 p-3">
                    <div class="flex-shrink-0">
                        <div class="icon-box" style="background-color: {{ $config['bg'] }}; color: {{ $config['color'] }};">
                            <i class="fa {{ $config['icon'] }}"></i>
                        </div>
                    </div>

                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.95rem;">{{ $log->description }}</h6>

                                {{-- Meta Tags --}}
                                @if ($log->meta)
                                    <div class="d-flex gap-2 flex-wrap my-2">
                                        @foreach ($log->meta as $key => $value)
                                            <span class="meta-tag">
                                                <span class="text-uppercase opacity-75" style="font-size: 9px">{{ str_replace('_', ' ', $key) }}:</span>
                                                <span class="fw-bold">{{ $value }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 0.8rem;">
                                    <span><i class="fa fa-circle-user me-1 text-primary opacity-50"></i> {{ $log->user?->name ?? 'System' }}</span>
                                    <span><i class="fa fa-calendar me-1 opacity-50"></i> {{ $log->created_at->format('d M, H:i') }}</span>
                                    <span class="fw-medium text-primary"><i class="fa fa-stopwatch me-1 opacity-50"></i>{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="ms-3">
                                @if ($log->status === 'success')
                                    <span class="badge-soft bg-success bg-opacity-10 text-success">
                                        <i class="fa fa-check-circle me-1"></i> Success
                                    </span>
                                @elseif ($log->status === 'failed')
                                    <span class="badge-soft bg-danger bg-opacity-10 text-danger">
                                        <i class="fa fa-times-circle me-1"></i> Failed
                                    </span>
                                @else
                                    <span class="badge-soft bg-warning bg-opacity-10 text-warning">
                                        <i class="fa fa-clock me-1"></i> Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fa-solid fa-inbox fa-4x text-light"></i>
                    </div>
                    <h5 class="text-muted">Tidak ada aktivitas ditemukan</h5>
                    <p class="text-muted small">Semua aktivitas bot akan muncul di sini secara otomatis.</p>
                </div>
            @endforelse
        </div>

        @if ($logs->hasPages())
            <div class="card-footer bg-white border-0 p-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

@endsection
