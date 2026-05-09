@extends('layout.sidebar')

@section('content')
    <style>
        :root {
            --receipt-bg: #f7fbff;
            --receipt-card: #ffffff;
            --receipt-line: #d8e9fb;
            --receipt-ink: #112b26;
            --receipt-ink-soft: #60727f;
            --receipt-primary: #00b7ff;
            --receipt-primary-dark: #008fe0;
            --receipt-success: #16a34a;
        }

        .receipt-shell {
            padding: 28px 12px 32px;
            min-height: calc(100vh - 74px);
            background: var(--receipt-bg);
        }

        .receipt-wrap {
            width: min(760px, 100%);
            margin: 0 auto;
        }

        .receipt-card {
            border: 1px solid var(--receipt-line);
            border-radius: 22px;
            background: var(--receipt-card);
            box-shadow: 0 20px 38px rgba(7, 61, 96, 0.1);
            overflow: hidden;
        }

        .receipt-head {
            padding: 26px 24px 22px;
            border-bottom: 1px solid #e9f1fb;
            background: linear-gradient(145deg, #f8fcff, #edf7ff);
            text-align: center;
        }

        .success-icon {
            width: 86px;
            height: 86px;
            margin: 0 auto 14px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: linear-gradient(145deg, #ecfdf3, #dcfce7);
            border: 1px solid #bbf7d0;
            color: var(--receipt-success);
            font-size: 2.4rem;
        }

        .receipt-title {
            margin: 0;
            font-weight: 800;
            color: var(--receipt-ink);
            letter-spacing: -0.02em;
        }

        .receipt-subtitle {
            margin: 8px 0 0;
            color: var(--receipt-ink-soft);
            font-size: 0.95rem;
        }

        .receipt-body {
            padding: 20px 24px;
        }

        .receipt-section {
            border: 1px solid #e8f1fb;
            border-radius: 14px;
            padding: 14px;
            background: #fbfdff;
        }

        .receipt-section+.receipt-section {
            margin-top: 14px;
        }

        .section-label {
            margin: 0 0 10px;
            font-size: 0.74rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #5d7385;
        }

        .receipt-item {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 7px 0;
            border-bottom: 1px dashed #d9e7f6;
            font-size: 0.88rem;
        }

        .receipt-item:last-child {
            border-bottom: 0;
            padding-bottom: 2px;
        }

        .receipt-item .k {
            color: var(--receipt-ink-soft);
        }

        .receipt-item .v {
            color: var(--receipt-ink);
            font-weight: 700;
            text-align: right;
        }

        .receipt-item .v.success {
            color: var(--receipt-success);
        }

        .access-list {
            display: grid;
            gap: 8px;
        }

        .access-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #2f4658;
            font-size: 0.9rem;
        }

        .access-item i.status-ok {
            color: #16a34a;
        }

        .access-item i.status-no {
            color: #dc2626;
        }

        .receipt-actions {
            display: flex;
            gap: 10px;
            padding: 18px 24px 24px;
            border-top: 1px solid #e9f1fb;
            background: #ffffff;
        }

        .btn-receipt-primary {
            border: 0;
            color: #fff;
            background: linear-gradient(145deg, var(--receipt-primary), var(--receipt-primary-dark));
            box-shadow: 0 10px 22px rgba(0, 151, 255, 0.28);
        }

        .btn-receipt-primary:hover {
            color: #fff;
            filter: brightness(1.03);
            transform: translateY(-1px);
        }

        .btn-receipt-outline {
            border: 1px solid #c5dbf4;
            color: #24435b;
            background: #f8fbff;
        }

        .btn-receipt-outline:hover {
            color: #173247;
            background: #eef6ff;
        }

        @media (max-width: 767.98px) {
            .receipt-shell {
                padding: 18px 8px 24px;
            }

            .receipt-head,
            .receipt-body,
            .receipt-actions {
                padding-left: 14px;
                padding-right: 14px;
            }

            .receipt-actions {
                flex-direction: column;
            }
        }
    </style>

    <div class="receipt-shell">
        <div class="receipt-wrap">
            <div class="receipt-card">
                <div class="receipt-head">
                    <div class="success-icon">
                        <i class="fa fa-circle-check"></i>
                    </div>
                    <h4 class="receipt-title">Pembayaran Berhasil</h4>
                    <p class="receipt-subtitle">Terima kasih, akses group kamu sudah aktif dan siap dipakai.</p>
                </div>

                <div class="receipt-body">
                    <div class="receipt-section">
                        <p class="section-label">Detail Pembayaran</p>
                        <div class="receipt-item">
                            <span class="k">Order ID</span>
                            <span class="v">{{ $payment->order_id }}</span>
                        </div>
                        <div class="receipt-item">
                            <span class="k">Jumlah</span>
                            <span class="v">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="receipt-item">
                            <span class="k">Tanggal</span>
                            <span class="v">{{ $payment->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="receipt-item">
                            <span class="k">Aktif Hingga</span>
                            <span class="v success">
                                {{ $payment->expires_at ? $payment->expires_at->format('d M Y') : ($payment->subscription->expires_at ? $payment->subscription->expires_at->format('d M Y') : '-') }}
                            </span>
                        </div>
                    </div>

                    <div class="receipt-section">
                        <p class="section-label">Detail Akses Group</p>
                        <div class="access-list">
                            <div class="access-item">
                                @if ($payment->subscription?->has_whatsapp)
                                    <i class="fa fa-circle-check status-ok"></i>
                                @else
                                    <i class="fa fa-circle-xmark status-no"></i>
                                @endif
                                <span><i class="fab fa-whatsapp text-success me-1"></i>WhatsApp Bot</span>
                            </div>

                            <div class="access-item">
                                @if ($payment->subscription?->has_discord)
                                    <i class="fa fa-circle-check status-ok"></i>
                                @else
                                    <i class="fa fa-circle-xmark status-no"></i>
                                @endif
                                <span><i class="fab fa-discord text-primary me-1"></i>Discord Bot</span>
                            </div>

                            <div class="access-item">
                                @if ($payment->subscription?->has_telegram)
                                    <i class="fa fa-circle-check status-ok"></i>
                                @else
                                    <i class="fa fa-circle-xmark status-no"></i>
                                @endif
                                <span><i class="fab fa-telegram text-info me-1"></i>Telegram Bot</span>
                            </div>

                            <div class="access-item">
                                <i class="fa fa-circle-check status-ok"></i>
                                <span><i class="fa fa-layer-group text-secondary me-1"></i>Akses
                                    {{ $payment->subscription?->max_groups }} Group</span>
                            </div>

                            <div class="access-item">
                                <i class="fa fa-circle-check status-ok"></i>
                                <span><i class="fa fa-users text-secondary me-1"></i>Max
                                    {{ $payment->subscription?->max_members }} Member per Group</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="receipt-actions">
                    <a href="/groups" class="btn btn-receipt-primary w-100">
                        <i class="fa fa-layer-group me-1"></i>Ke Groups
                    </a>
                    <a href="/payment/receipt/{{ $payment->order_id }}/print" class="btn btn-receipt-outline w-100">
                        <i class="fa fa-file-pdf me-1"></i>Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
