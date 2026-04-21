@extends('superadmin.layout')

@section('title', 'Users Management')
@section('page_title', 'Users Management & Monitoring')

@section('css')
    <style>
        .search-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .users-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .users-table table {
            margin-bottom: 0;
        }

        .users-table thead {
            background: #f8fafc;
        }

        .users-table th {
            border: none;
            padding: 15px;
            font-weight: 600;
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .users-table td {
            border: none;
            padding: 15px;
            color: #475569;
        }

        .user-name {
            font-weight: 600;
            color: #1a202c;
            display: block;
        }

        .user-email {
            font-size: 12px;
            color: #64748b;
        }

        .subscription-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .subscription-active {
            background: #dcfce7;
            color: #166534;
        }

        .subscription-expired {
            background: #fee2e2;
            color: #991b1b;
        }

        .subscription-none {
            background: #f1f5f9;
            color: #64748b;
        }

        .badges-group {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .bot-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
        }

        .bot-wa {
            background: #dcfce7;
            color: #166534;
        }

        .bot-discord {
            background: #dbeafe;
            color: #0c4a6e;
        }

        .bot-telegram {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-view {
            background: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-view:hover {
            background: #2563eb;
            color: white;
        }

        .search-form {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        .form-group {
            margin-bottom: 0;
            flex: 1;
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

        .btn-search {
            background: #7c3aed;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-search:hover {
            background: #6d28d9;
        }
    </style>
@endsection

@section('content')
    <div class="search-card">
        <form action="{{ route('superadmin.users.index') }}" method="GET" class="search-form">
            <div class="form-group" style="flex: 1;">
                <label for="search">Search by Name or Email</label>
                <input type="text" id="search" name="search" class="form-control" placeholder="Enter name or email..."
                    value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-search">
                <i class="fas fa-search me-2"></i>Search
            </button>
            @if (request('search'))
                <a href="{{ route('superadmin.users.index') }}" class="btn-search"
                    style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;">
                    <i class="fas fa-times"></i>Reset
                </a>
            @endif
        </form>
    </div>

    <div class="users-table">
        @if ($users->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox text-muted" style="font-size: 48px;"></i>
                <p class="text-muted mt-3">No users found</p>
            </div>
        @else
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Subscription</th>
                        <th>Bots</th>
                        <th>Payments</th>
                        <th>Joined</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <span class="user-name">{{ $user->name }}</span>
                            </td>
                            <td>
                                <span class="user-email">{{ $user->email }}</span>
                            </td>
                            <td>
                                @if ($user->activeSubscription)
                                    <span class="subscription-badge subscription-active">
                                        <i class="fas fa-check-circle me-1"></i>Active
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        Until {{ $user->activeSubscription->expires_at->format('d M Y') }}
                                    </small>
                                @else
                                    <span class="subscription-badge subscription-none">No Subscription</span>
                                @endif
                            </td>
                            <td>
                                <div class="badges-group">
                                    @if ($user->activeSubscription?->has_whatsapp)
                                        <span class="bot-badge bot-wa">WA</span>
                                    @endif
                                    @if ($user->activeSubscription?->has_discord)
                                        <span class="bot-badge bot-discord">Discord</span>
                                    @endif
                                    @if ($user->activeSubscription?->has_telegram)
                                        <span class="bot-badge bot-telegram">TG</span>
                                    @endif
                                    @if (!$user->activeSubscription)
                                        <span style="font-size: 11px; color: #64748b;">-</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong>{{ $user->payments_count ?? $user->payments()->count() }}</strong>
                            </td>
                            <td>
                                <small>{{ $user->created_at->format('d M Y') }}</small>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('superadmin.users.show', $user) }}" class="btn-view">
                                    <i class="fas fa-eye"></i>View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($users->hasPages())
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif
@endsection
