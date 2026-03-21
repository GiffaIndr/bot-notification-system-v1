<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt_{{ $payment->order_id }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            padding: 40px 20px;
            color: #334155;
        }

        .container {
            max-width: 550px;
            margin: 0 auto;
            position: relative;
        }

        /* Dekorasi Lubang Struk (Samping) */
        .receipt-main {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            position: relative;
        }

        /* Header Blue Bar */
        .top-bar {
            height: 8px;
            background: linear-gradient(90deg, #6366f1, #a855f7);
        }

        .content { padding: 40px; }

        /* Brand & Status */
        .header-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .brand-side { display: table-cell; vertical-align: middle; }
        .status-side { display: table-cell; text-align: right; vertical-align: middle; }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.5px;
        }

        .badge-paid {
            background: #dcfce7;
            color: #15803d;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border: 1px solid #bbf7d0;
        }

        /* Table Style Info */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .info-table td { padding: 8px 0; font-size: 13px; }
        .info-table td.label { color: #64748b; width: 40%; }
        .info-table td.value { color: #1e293b; font-weight: 600; text-align: right; }

        /* Divider */
        .divider {
            border-top: 1px dashed #e2e8f0;
            margin: 20px 0;
            position: relative;
        }
        .divider::before, .divider::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background: #f1f5f9; /* Sama dengan bg body */
            border-radius: 50%;
            top: -10px;
        }
        .divider::before { left: -50px; border-right: 1px solid #e2e8f0; }
        .divider::after { right: -50px; border-left: 1px solid #e2e8f0; }

        /* Pricing Box */
        .price-display {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px 20px;
            text-align: center;
            margin-top: 5px;
        }
        .price-label { font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
        .price-amount { font-size: 24px; font-weight: 800; color: #4f46e5; }

        /* Features */
        .feature-title { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 1px; }
        .feature-item {
            display: inline-block;
            width: 48%;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 500;
        }

        /* Pure CSS Icons */
        .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 8px; }
        .dot-check { background: #22c55e; box-shadow: 0 0 0 3px #dcfce7; }
        .dot-cross { background: #ef4444; box-shadow: 0 0 0 3px #fee2e2; }

        .footer-note {
            text-align: center;
            margin-top: 30px;
            font-size: 11px;
            color: #94a3b8;
        }

        .order-id { font-family: 'Monaco', monospace; font-size: 12px; color: #6366f1; }

        @media print {
            body { background: white; padding: 0; }
            .receipt-main { box-shadow: none; border: 1px solid #eee; }
            .divider::before, .divider::after { display: none; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="receipt-main">
        <div class="top-bar"></div>

        <div class="content">
            <div class="header-grid">
                <div class="brand-side">
                    <div class="brand-name">AnnounceBot</div>
                    <div class="order-id">#{{ $payment->order_id }}</div>
                </div>
                <div class="status-side">
                    <span class="badge-paid">Paid Successful</span>
                </div>
            </div>

            <table class="info-table">
                <tr>
                    <td class="label">Customer Name</td>
                    <td class="value">{{ $payment->user->name }}</td>
                </tr>
                <tr>
                    <td class="label">Email Address</td>
                    <td class="value">{{ $payment->user->email }}</td>
                </tr>
                <tr>
                    <td class="label">Transaction Date</td>
                    <td class="value">{{ $payment->created_at->format('d M Y, H:i') }}</td>
                </tr>
                <tr>
                    <td class="label">Valid Until</td>
                    <td class="value" style="color: #22c55e;">{{ $payment->expires_at ? $payment->expires_at->format('d M Y') : ($payment->subscription?->expires_at?->format('d M Y') ?? '-') }}</td>
                </tr>
            </table>

            <div class="price-display">
                <div class="price-label">Total Payment</div>
                <div class="price-amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
            </div>

            <div class="divider"></div>

            <div class="feature-title">Subscription Benefits</div>
            <div class="feature-list">
                <div class="feature-item">
                    <span class="dot {{ $payment->subscription?->has_whatsapp ? 'dot-check' : 'dot-cross' }}"></span>
                    WhatsApp Bot
                </div>
                <div class="feature-item">
                    <span class="dot {{ $payment->subscription?->has_discord ? 'dot-check' : 'dot-cross' }}"></span>
                    Discord Bot
                </div>
                <div class="feature-item">
                    <span class="dot {{ $payment->subscription?->has_telegram ? 'dot-check' : 'dot-cross' }}"></span>
                    Telegram Bot
                </div>
                <div class="feature-item">
                    <span class="dot dot-check"></span>
                    {{ $payment->subscription?->max_groups ?? '0' }} Groups Limit
                </div>
            </div>
        </div>
    </div>

    <div class="footer-note">
        <p>Thank you for your business!</p>
        <p>Generated on {{ now()->format('d M Y, H:i') }}</p>
    </div>
</div>

</body>
</html>
