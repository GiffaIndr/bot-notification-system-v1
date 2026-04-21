@extends('superadmin.layout')

@section('title', 'Activity Logs')
@section('page_title', 'Activity Logs Monitoring')

@section('css')
    <style>
        .filter-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .logs-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .logs-table table {
            margin-bottom: 0;
        }

        .logs-table thead {
            background: #f8fafc;
        }

        .logs-table th {
            border: none;
            padding: 15px;
            font-weight: 600;
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .logs-table td {
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

        .user-info {
            line-height: 1.4;
        }

        .user-info strong {
            display: block;
            color: #1a202c;
        }

        .user-email {
            font-size: 12px;
            color: #64748b;
        }

        .ip-code {
            font-family: 'Courier New', monospace;
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        }

        .form-control {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 13px;
        }

        .form-control:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .btn-filter {
            background: #7c3aed;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-filter:hover {
            background: #6d28d9;
        }

        .btn-reset {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-reset:hover {
            border-color: #cbd5e1;
            color: #1a202c;
        }
    </style>
@endsection

@section('content')
    <div class="filter-card">
        <h6 class="mb-3"><i class="fas fa-filter me-2"></i>Filter Activity Logs</h6>
        <form action="{{ route('superadmin.activity-logs') }}" method="GET" class="filter-row">
            <div class="form-group">
                <label for="action">Action</label>
                <select id="action" name="action" class="form-control">
                    <option value="">All Actions</option>
                    <option value="update_pricing" {{ request('action') == 'update_pricing' ? 'selected' : '' }}>
                        Update Pricing
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="user_id">User</label>
                <select id="user_id" name="user_id" class="form-control">
                    <option value="">All Users</option>
                    @foreach ($users as $userId => $userName)
                        <option value="{{ $userId }}" {{ request('user_id') == $userId ? 'selected' : '' }}>
                            {{ $userName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="date_from">Date From</label>
                <input type="date" id="date_from" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="form-group">
                <label for="date_to">Date To</label>
                <input type="date" id="date_to" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="{{ route('superadmin.activity-logs') }}" class="btn-reset">
                    <i class="fas fa-times me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <div class="logs-table">
        @if ($logs->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox text-muted" style="font-size: 48px;"></i>
                <p class="text-muted mt-3">No activity logs found</p>
            </div>
        @else
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>User</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>
                                @php
                                    $actionClass = match($log->action) {
                                        'update_pricing' => 'action-update',
                                        default => 'action-update'
                                    };
                                @endphp
                                <span class="action-badge {{ $actionClass }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                @if ($log->user)
                                    <div class="user-info">
                                        <strong>{{ $log->user->name }}</strong>
                                        <span class="user-email">{{ $log->user->email }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $log->description }}</td>
                            <td><span class="ip-code">{{ $log->ip_address }}</span></td>
                            <td>
                                <small>{{ $log->created_at->format('d M Y H:i:s') }}</small>
                                <br>
                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($logs->hasPages())
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @endif
@endsection
