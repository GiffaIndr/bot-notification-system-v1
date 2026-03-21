@extends('layout.sidebar')

@section('content')
<style>
    /* 1. Layout & Timeline Styling */
    .activity-log-container {
        position: relative;
        padding-left: 1rem;
    }

    /* Garis vertikal timeline yang presisi */
    .activity-log-container::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 3.1rem;
        width: 2px;
        background: linear-gradient(to bottom, #f1f5f9 0%, #e2e8f0 50%, #f1f5f9 100%);
    }

    .log-item {
        position: relative;
        z-index: 1;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 16px;
        border: 1px solid transparent;
        margin-bottom: 10px;
    }

    .log-item:hover {
        background-color: white;
        border-color: #f1f5f9;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        transform: translateX(5px);
    }

    /* 2. Icon Box Styling */
    .icon-box {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 1.1rem;
        border: 4px solid white; /* Membuat celah putih di garis timeline */
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* 3. Status Badge - Fixed & Clean */
    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.5px;
        min-width: 100px; /* Menjaga agar sejajar lurus ke bawah */
        border-width: 1px;
        border-style: solid;
    }

    /* 4. Meta Tag Styling */
    .meta-tag {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        color: #475569;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .meta-key {
        color: #94a3b8;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 9px;
    }

    /* 5. Pagination Styling - Clean Bootstrap 5 */
    .pagination {
        margin-bottom: 0;
        gap: 5px;
    }

    .pagination .page-item .page-link {
        border: none;
        padding: 8px 16px;
        border-radius: 10px;
        color: #64748b;
        font-weight: 600;
        background: #f1f5f9;
        font-size: 13px;
        transition: all 0.2s;
    }

    .pagination .page-item.active .page-link {
        background-color: #4f46e5;
        color: white;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    .pagination .page-item.disabled .page-link {
        background: transparent;
        color: #cbd5e1;
    }

    /* Fix panah SVG bawaan Laravel yang sering kebesaran */
    .pagination svg {
        width: 18px;
        height: 18px;
        vertical-align: middle;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="/groups" class="text-muted text-decoration-none">Groups</a></li>
                    <li class="breadcrumb-item active fw-bold text-primary">{{ $group->name }}</li>
                </ol>
            </nav>
            <h3 class="fw-bold m-0 text-dark">Activity Log</h3>
        </div>
        <div>
            <span class="badge bg-white shadow-sm text-primary px-3 py-2 rounded-pill border-0 fw-bold">
                <i class="fa fa-layer-group me-1"></i> {{ $group->name }}
            </span>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 24px;">
        <div class="card-header bg-white py-4 px-4 border-bottom border-light d-flex justify-content-between align-items-center">
            <h6 class="fw-bold m-0"><i class="fa fa-history text-primary me-2"></i>Riwayat Aktivitas</h6>
            <span class="text-muted small">Total <b>{{ $logs->total() }}</b> records</span>
        </div>

        <div class="card-body p-4 activity-log-container">
            @forelse ($logs as $log)
                @php
                    $config = match($log->type) {
                        'create_announcement' => ['icon' => 'fa-plus', 'color' => '#4f46e5', 'bg' => '#eef2ff'],
                        'edit_announcement'   => ['icon' => 'fa-pen-nib', 'color' => '#d97706', 'bg' => '#fffbeb'],
                        'delete_announcement' => ['icon' => 'fa-trash-alt', 'color' => '#dc2626', 'bg' => '#fef2f2'],
                        'bot_connected'       => ['icon' => 'fa-robot', 'color' => '#0891b2', 'bg' => '#ecfeff'],
                        'generate_code'       => ['icon' => 'fa-shield-check', 'color' => '#7c3aed', 'bg' => '#f5f3ff'],
                        'notification_sent'   => ['icon' => 'fa-paper-plane', 'color' => '#059669', 'bg' => '#ecfdf5'],
                        default               => ['icon' => 'fa-info-circle', 'color' => '#475569', 'bg' => '#f8fafc'],
                    };
                @endphp

                <div class="log-item d-flex gap-4 p-3">
                    <div class="flex-shrink-0">
                        <div class="icon-box" style="background-color: {{ $config['bg'] }}; color: {{ $config['color'] }};">
                            <i class="fa {{ $config['icon'] }}"></i>
                        </div>
                    </div>

                    <div class="flex-grow-1 pt-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="w-100">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="mb-0 fw-bold text-dark">{{ $log->description }}</h6>
                                    @if($log->created_at->isToday())
                                        <span class="badge bg-primary rounded-pill" style="font-size: 8px;">HARI INI</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center gap-3 text-muted mb-2" style="font-size: 0.75rem;">
                                    <span><i class="fa fa-user-circle me-1 text-primary"></i> <b>{{ $log->user?->name ?? 'System' }}</b></span>
                                    <span><i class="far fa-clock me-1"></i> {{ $log->created_at->format('H:i') }}</span>
                                    <span class="text-primary fw-medium">{{ $log->created_at->diffForHumans() }}</span>
                                </div>

                                @if ($log->meta)
                                    <div class="d-flex gap-2 flex-wrap mt-2">
                                        @foreach ($log->meta as $key => $value)
                                            <div class="meta-tag">
                                                <span class="meta-key">{{ str_replace('_', ' ', $key) }}</span>
                                                <span class="fw-bold">{{ $value }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="ms-auto text-end">
                                @if ($log->status === 'success')
                                    <div class="status-badge bg-success bg-opacity-10 text-success border-success border-opacity-10">
                                        <i class="fa fa-check-circle"></i> SUCCESS
                                    </div>
                                @elseif ($log->status === 'failed')
                                    <div class="status-badge bg-danger bg-opacity-10 text-danger border-danger border-opacity-10">
                                        <i class="fa fa-times-circle"></i> FAILED
                                    </div>
                                @else
                                    <div class="status-badge bg-warning bg-opacity-10 text-warning border-warning border-opacity-10">
                                        <i class="fa fa-spinner fa-spin"></i> PENDING
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fa fa-folder-open fa-3x text-light mb-3"></i>
                    <h5 class="text-dark fw-bold">Belum ada aktivitas</h5>
                    <p class="text-muted small">Log interaksi bot akan muncul otomatis di sini.</p>
                </div>
            @endforelse
        </div>

        @if ($logs->hasPages())
            <div class="card-footer bg-white border-top border-light py-4">
                <div class="d-flex flex-column align-items-center gap-3">
                    <div class="text-muted small">
                        Showing <b>{{ $logs->firstItem() }}</b> to <b>{{ $logs->lastItem() }}</b> of <b>{{ $logs->total() }}</b> results
                    </div>
                    {{ $logs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
