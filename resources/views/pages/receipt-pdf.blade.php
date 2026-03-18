<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }

        .receipt {
            max-width: 400px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #ddd;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .status {
            text-align: center;
            margin-bottom: 20px;
        }

        .status .badge {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .section-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #999;
            letter-spacing: 1px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .detail-row .label { color: #666; }
        .detail-row .value { font-weight: bold; }

        .divider {
            border: none;
            border-top: 1px dashed #ddd;
            margin: 15px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }

        .features {
            margin-top: 15px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 6px;
            font-size: 11px;
        }

        .feature-item .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .dot-success { background: #10b981; }
        .dot-failed  { background: #ef4444; }

        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 2px dashed #ddd;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="receipt">

        {{-- Header --}}
        <div class="header">
            <h1>Bot Notification System</h1>
            <p>Bukti Pembayaran Resmi</p>
        </div>

        {{-- Status --}}
        <div class="status">
            <span class="badge">✓ Pembayaran Berhasil</span>
        </div>

        {{-- Detail Transaksi --}}
        <div class="section-title">Detail Transaksi</div>

        <div class="detail-row">
            <span class="label">Order ID</span>
            <span class="value">{{ $payment->order_id }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Tanggal</span>
            <span class="value">{{ $payment->created_at->format('d M Y, H:i') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Nama</span>
            <span class="value">{{ $payment->user->name }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Email</span>
            <span class="value">{{ $payment->user->email }}</span>
        </div>

        <hr class="divider">

        {{-- Detail Plan --}}
        <div class="section-title">Detail Langganan</div>

        <div class="detail-row">
            <span class="label">Plan</span>
            <span class="value">{{ $payment->plan->name }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Periode</span>
            <span class="value">6 Bulan</span>
        </div>
        <div class="detail-row">
            <span class="label">Aktif Dari</span>
            <span class="value">{{ $payment->starts_at->format('d M Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Aktif Hingga</span>
            <span class="value">{{ $payment->expires_at->format('d M Y') }}</span>
        </div>

        <hr class="divider">

        {{-- Total --}}
        <div class="total-row">
            <span>Total</span>
            <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
        </div>

        <hr class="divider">

        {{-- Fitur --}}
        <div class="section-title">Fitur Yang Didapat</div>

        <div class="features">
            <div class="feature-item">
                <span class="dot {{ $payment->plan->whatsapp ? 'dot-success' : 'dot-failed' }}"></span>
                WhatsApp Bot
            </div>
            <div class="feature-item">
                <span class="dot {{ $payment->plan->discord ? 'dot-success' : 'dot-failed' }}"></span>
                Discord Bot
            </div>
            <div class="feature-item">
                <span class="dot {{ $payment->plan->telegram ? 'dot-success' : 'dot-failed' }}"></span>
                Telegram Bot
            </div>
            <div class="feature-item">
                <span class="dot dot-success"></span>
                Max {{ $payment->plan->max_group }} Group
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Dokumen ini merupakan bukti pembayaran yang sah.</p>
            <p>Dicetak pada {{ now()->format('d M Y, H:i') }}</p>
        </div>

    </div>
</body>
</html>
