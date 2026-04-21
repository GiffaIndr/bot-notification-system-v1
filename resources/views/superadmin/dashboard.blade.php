@extends('superadmin.layout')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('css')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .stat-icon.blue {
            background: #dbeafe;
            color: #0284c7;
        }

        .stat-icon.green {
            background: #dcfce7;
            color: #16a34a;
        }

        .stat-icon.purple {
            background: #f3e8ff;
            color: #a855f7;
        }

        .stat-icon.orange {
            background: #fed7aa;
            color: #ea580c;
        }

        .stat-label {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
        }

        .activity-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .activity-table table {
            margin-bottom: 0;
        }

        .activity-table thead {
            background: #f8fafc;
        }

        .activity-table th {
            border: none;
            padding: 15px;
            font-weight: 600;
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .activity-table td {
            border: none;
            padding: 15px;
            color: #475569;
        }

        .action-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .action-update {
            background: #fef3c7;
            color: #92400e;
        }

        .action-create {
            background: #dcfce7;
            color: #166534;
        }

        .action-delete {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
@endsection

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-label">Total Users</div>
            <div class="stat-value">{{ $total_users }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-label">Active Subscriptions</div>
            <div class="stat-value">{{ $active_subscriptions }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">
                Rp {{ number_format($total_revenue, 0, ',', '.') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-label">Pending Payments</div>
            <div class="stat-value">{{ $pending_payments }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h5>
        </div>
        <div class="card-body p-0">
            @if ($activity_logs->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-3">No activity found</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Action</th>
                                <th>User</th>
                                <th>Description</th>
                                <th>IP Address</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activity_logs as $log)
                                <tr>
                                    <td>
                                        @php
                                            $actionClass = match($log->action) {
                                                'update_pricing' => 'action-update',
                                                'create_*' => 'action-create',
                                                'delete_*' => 'action-delete',
                                                default => 'action-update'
                                            };
                                        @endphp
                                        <span class="action-badge {{ $actionClass }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($log->user)
                                            <strong>{{ $log->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $log->user->email }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td><code>{{ $log->ip_address }}</code></td>
                                    <td>{{ $log->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('superadmin.activity-logs') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right me-2"></i>View All Activity Logs
        </a>
    </div>
@endsection
