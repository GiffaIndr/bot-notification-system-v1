@extends('superadmin.layout')

@section('title', 'User - ' . $user->name)
@section('page_title', 'User Details - ' . $user->name)

@section('css')
    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .user-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .avatarbox {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .user-name {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 5px;
        }

        .user-email {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .info-item {
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 14px;
            color: #1a202c;
            font-weight: 500;
        }

        .subscription-section .card {
            border: none;
        }

        .subscription-status {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-expired {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-none {
            background: #f1f5f9;
            color: #64748b;
        }

        .bot-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .bot-tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
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

        .payments-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .payments-table table {
            margin-bottom: 0;
        }

        .payments-table thead {
            background: #f8fafc;
        }

        .payments-table th {
            border: none;
            padding: 15px;
            font-weight: 600;
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .payments-table td {
            border: none;
            padding: 15px;
            color: #475569;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #7c3aed;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            color: #6d28d9;
        }
    </style>
@endsection

@section('content')
    <a href="{{ route('superadmin.users.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i>Back to Users
    </a>

    <div class="detail-grid">
        <div>
            <div class="user-card">
                <div class="avatarbox">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <div class="user-name">{{ $user->name }}</div>
                <div class="user-email">{{ $user->email }}</div>

                <div class="info-item">
                    <div class="info-label">Phone</div>
                    <div class="info-value">{{ $user->phone ?? '-' }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Joined</div>
                    <div class="info-value">{{ $user->created_at->format('d M Y, H:i') }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Email Verified</div>
                    <div class="info-value">
                        @if ($user->email_verified_at)
                            <span style="color: #10b981;"><i class="fas fa-check-circle"></i> Yes</span>
                            <br>
                            <small class="text-muted">{{ $user->email_verified_at->format('d M Y') }}</small>
                        @else
                            <span style="color: #ef4444;"><i class="fas fa-times-circle"></i> No</span>
                        @endif
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Total Payments</div>
                    <div class="info-value">{{ $user->payments()->count() }} transaction(s)</div>
                </div>
            </div>
        </div>

        <div>
            <div class="user-card subscription-section">
                <h6 class="mb-3"><i class="fas fa-check-circle me-2"></i>Active Subscription</h6>

                @if ($user->activeSubscription)
                    <span class="subscription-status status-active">ACTIVE</span>

                    <div class="info-item">
                        <div class="info-label">Subscription ID</div>
                        <div class="info-value">{{ $user->activeSubscription->id }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Active From</div>
                        <div class="info-value">{{ $user->activeSubscription->starts_at->format('d M Y') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Expires At</div>
                        <div class="info-value">{{ $user->activeSubscription->expires_at->format('d M Y') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Days Remaining</div>
                        <div class="info-value">
                            {{ $user->activeSubscription->expires_at->diffInDays(now()) }} days
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Bots Enabled</div>
                        <div class="bot-list">
                            @if ($user->activeSubscription->has_whatsapp)
                                <span class="bot-tag bot-wa"><i class="fab fa-whatsapp me-1"></i>WhatsApp</span>
                            @endif
                            @if ($user->activeSubscription->has_discord)
                                <span class="bot-tag bot-discord"><i class="fab fa-discord me-1"></i>Discord</span>
                            @endif
                            @if ($user->activeSubscription->has_telegram)
                                <span class="bot-tag bot-telegram"><i class="fab fa-telegram me-1"></i>Telegram</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Capacity</div>
                        <div class="info-value">
                            {{ $user->activeSubscription->max_groups }} group(s) &bull; {{ $user->activeSubscription->max_members }} member(s)
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Total Price</div>
                        <div class="info-value" style="font-size: 16px; font-weight: 700; color: #7c3aed;">
                            Rp {{ number_format($user->activeSubscription->total_price, 0, ',', '.') }}
                        </div>
                    </div>
                @else
                    <span class="subscription-status status-none">NO SUBSCRIPTION</span>
                    <p class="text-muted mt-3">User belum memiliki subscription aktif.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-history me-2"></i>Payment History</h6>
        </div>
        <div class="card-body p-0">
            @if ($payments->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-3">No payments found</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>
                                        <code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px;">
                                            {{ $payment->order_id }}
                                        </code>
                                    </td>
                                    <td>
                                        <strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        @if ($payment->status === 'success')
                                            <span class="badge bg-success">Success</span>
                                        @elseif ($payment->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->created_at->format('d M Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @if ($payments->hasPages())
        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    @endif
@endsection
