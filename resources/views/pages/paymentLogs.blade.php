@extends('layout.sidebar')

@section('content')
    <style>
        /* Table Styling */
        .table-custom {
            border-collapse: separate;
            border-spacing: 0 8px;
            /* Memberi jarak antar baris */
        }

        .table-custom thead th {
            border: none;
            color: #64748b;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px 20px;
        }

        .table-custom tbody tr {
            background: white;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .table-custom tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            background-color: #f8fafc;
        }

        .table-custom td {
            padding: 15px 20px !important;
            vertical-align: middle;
            border: none;
        }

        .table-custom td:first-child {
            border-radius: 12px 0 0 12px;
        }

        .table-custom td:last-child {
            border-radius: 0 12px 12px 0;
        }

        /* Order ID Styling */
        .order-id {
            font-family: 'Monaco', 'Consolas', monospace;
            color: #6366f1;
            font-weight: 600;
            background: #eef2ff;
            padding: 4px 8px;
            border-radius: 6px;
        }

        /* Action Buttons */
        .btn-action {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all 0.2s;
        }

        /* Plan Badges - Colorful */
        .plan-badge-pro {
            background: linear-gradient(135deg, #a855f7, #6366f1);
            color: white;
        }

        .plan-badge-basic {
            background: #e2e8f0;
            color: #475569;
        }
    </style>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="/dashboard" class="text-muted text-decoration-none">
                <i class="fa fa-arrow-left me-1"></i> Dashboard
            </a>
            <h3 class="mt-1 mb-0 fw-bold">Riwayat Pembayaran</h3>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header fw-bold bg-white border-bottom">
            <i class="fa fa-receipt text-primary me-2"></i>Log Pembayaran
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Plan</th>
                        <th>Jumlah</th>
                        <th>Aktif</th>
                        <th>Expired</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="small">{{ $log->order_id }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $log->plan->name }}</span>
                            </td>
                            <td class="small">Rp {{ number_format($log->amount, 0, ',', '.') }}</td>
                            <td class="small">{{ $log->starts_at->format('d M Y') }}</td>
                            <td class="small">{{ $log->expires_at->format('d M Y') }}</td>
                            <td>
                                @if ($log->status === 'success')
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="fa fa-circle-check me-1"></i>Sukses
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger">
                                        <i class="fa fa-circle-xmark me-1"></i>Gagal
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="/payment/receipt/{{ $log->order_id }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="/payment/receipt/{{ $log->order_id }}/print"
                                        class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa fa-receipt fa-2x mb-2"></i>
                                <p class="mb-0">Belum ada riwayat pembayaran.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
            <div class="card-footer bg-white">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
