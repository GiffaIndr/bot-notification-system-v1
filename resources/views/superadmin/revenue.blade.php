@extends('superadmin.layout')

@section('title', 'Revenue Reports')
@section('page_title', 'Revenue & Transaction Reports')

@section('css')
    <style>
        .report-header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .stat-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #7c3aed;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            align-items: flex-end;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group label {
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 8px;
            font-size: 13px;
            display: block;
        }

        .form-control {
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            width: 100%;
        }

        .form-control:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .btn-filter {
            background: #7c3aed;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-filter:hover {
            background: #6d28d9;
        }

        .btn-export {
            background: #10b981;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-export:hover {
            background: #059669;
        }

        .transactions-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .transactions-table table {
            margin-bottom: 0;
        }

        .transactions-table thead {
            background: #f8fafc;
        }

        .transactions-table th {
            border: none;
            padding: 15px;
            font-weight: 600;
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .transactions-table td {
            border: none;
            padding: 15px;
            color: #475569;
        }

        .amount {
            font-weight: 700;
            color: #10b981;
        }

        .order-id {
            font-family: 'Courier New', monospace;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-success {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-failed {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
@endsection

@section('content')
    <div class="report-header">
        <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Report Filter</h5>
        <form action="{{ route('superadmin.revenue') }}" method="GET" class="filter-form">
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control"
                    value="{{ $startDate?->format('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control"
                    value="{{ $endDate?->format('Y-m-d') }}">
            </div>
            <button type="submit" class="btn-filter">
                <i class="fas fa-search me-2"></i>Filter
            </button>
            <a href="{{ route('superadmin.revenue') }}" class="btn-filter"
                style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; text-decoration: none;">
                <i class="fas fa-times"></i>Reset
            </a>
        </form>
    </div>

    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-label"><i class="fas fa-wallet me-1"></i>Total Revenue</div>
            <div class="stat-value">Rp {{ number_format($statistics['total_revenue'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label"><i class="fas fa-exchange-alt me-1"></i>Total Transactions</div>
            <div class="stat-value">{{ $statistics['total_transactions'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label"><i class="fas fa-chart-line me-1"></i>Average Transaction</div>
            <div class="stat-value">Rp {{ number_format($statistics['average_transaction'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="transactions-table">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>
                            <span class="order-id">{{ $payment->order_id }}</span>
                        </td>
                        <td>
                            <strong>{{ $payment->user->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $payment->user->email }}</small>
                        </td>
                        <td>
                            <span class="amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            @if ($payment->status === 'success')
                                <span class="status-badge status-success">Success</span>
                            @elseif ($payment->status === 'pending')
                                <span class="status-badge status-pending">Pending</span>
                            @else
                                <span class="status-badge status-failed">Failed</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $payment->created_at->format('d M Y H:i:s') }}</small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-inbox text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-3">No transactions found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($payments->hasPages())
        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    @endif
@endsection
