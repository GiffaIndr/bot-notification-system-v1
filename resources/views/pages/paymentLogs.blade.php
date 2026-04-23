@extends('layout.sidebar')

@section('content')
<style>
    /* Background Page */
    body { background-color: #f8fafc; }

    /* Modern Table Styling */
    .table-custom {
        border-separate: separate;
        border-spacing: 0 12px;
        width: 100%;
    }

    .table-custom thead th {
        border: none;
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 10px 20px;
    }

    .table-custom tbody tr {
        background: white;
        transition: all 0.2s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .table-custom tbody tr:hover {
        transform: scale(1.005);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        z-index: 1;
    }

    .table-custom td {
        padding: 16px 20px !important;
        vertical-align: middle;
        border: none;
    }

    .table-custom td:first-child { border-radius: 12px 0 0 12px; }
    .table-custom td:last-child { border-radius: 0 12px 12px 0; }

    /* ID Styling */
    .order-id {
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
        color: var(--tasku-deep);
        font-weight: 600;
        background: #e9f4fa;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.85rem;
    }

    /* Status Badges Enhancement */
    .badge-status {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Custom Pagination Styling */
    .pagination { gap: 5px; }
    .pagination .page-item .page-link {
        border: none;
        border-radius: 8px;
        color: #64748b;
        padding: 8px 16px;
    }
    .pagination .page-item.active .page-link {
        background-color: var(--tasku-primary);
        color: white;
        box-shadow: 0 4px 10px rgba(51, 118, 163, 0.3);
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Riwayat Pembayaran</h3>
        </div>
        <button class="btn btn-white shadow-sm border-0 px-3 py-2" onclick="location.reload()">
            <i class="fa fa-sync-alt text-muted"></i>
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Detail Fitur</th>
                    <th>Nominal</th>
                    <th>Periode Aktif</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                <tr>
                    <td>
                        <span class="order-id">#{{ $log->order_id }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            @if ($log->subscription?->has_whatsapp)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size:10px">
                                    <i class="fab fa-whatsapp"></i> WA
                                </span>
                            @endif
                            @if ($log->subscription?->has_discord)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size:10px">
                                    <i class="fab fa-discord"></i> Discord
                                </span>
                            @endif
                            <span class="badge bg-light text-dark border" style="font-size:10px">
                                {{ $log->subscription?->max_groups ?? '0' }} Groups
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">Rp {{ number_format($log->amount, 0, ',', '.') }}</div>
                    </td>
                    <td>
                        <div class="small text-muted">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ $log->starts_at?->format('d M Y') ?? '-' }}
                        </div>
                        <div class="small text-danger">
                            <i class="fa fa-arrow-right me-1" style="font-size: 10px"></i>
                            {{ $log->expires_at?->format('d M Y') ?? '-' }}
                        </div>
                    </td>
                    <td>
                        @if ($log->status === 'success')
                            <span class="badge-status bg-success bg-opacity-10 text-success">
                                <i class="fa fa-circle" style="font-size: 8px"></i> Berhasil
                            </span>
                        @elseif ($log->status === 'pending')
                            <span class="badge-status bg-warning bg-opacity-10 text-warning">
                                <i class="fa fa-circle" style="font-size: 8px"></i> Pending
                            </span>
                        @else
                            <span class="badge-status bg-danger bg-opacity-10 text-danger">
                                <i class="fa fa-circle" style="font-size: 8px"></i> Gagal
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($log->status === 'success')
                            <a href="/payment/receipt/{{ $log->order_id }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                <i class="fa fa-file-invoice me-1"></i> Invoice
                            </a>
                        @else
                            <button class="btn btn-sm btn-light rounded-pill disabled"><i class="fa fa-ban"></i></button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" class="mb-3 opacity-50">
                        <p class="text-muted">Belum ada transaksi yang ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
        <div class="text-muted small">
            Menampilkan {{ $logs->firstItem() }} sampai {{ $logs->lastItem() }} dari {{ $logs->total() }} data
        </div>
        <div>
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
