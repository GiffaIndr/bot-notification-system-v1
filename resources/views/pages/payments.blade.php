@extends('layout.sidebar')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Manrope:wght@400;600;700;800&display=swap');

        :root {
            --bg: #eef8ff;
            --ink: #102022;
            --ink-soft: #52666c;
            --line: #cfe6ff;
            --primary: #00b7ff;
            --primary-dark: #008fe0;
            --muted-chip: #e6f4ff;
        }

        .payments-shell {
            font-family: 'Manrope', sans-serif;
            background: transparent;
            min-height: calc(100vh - 60px);
            padding-bottom: 24px;
        }

        .section-card {
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 24px rgba(12, 59, 48, 0.08);
        }

        .payments-hero {
            padding: 18px;
            border-radius: 18px;
            border: 1px solid #b9dcfb;
            background: linear-gradient(145deg, #f8fcff, #ebf6ff);
        }

        .payments-hero h4 {
            font-family: 'Space Grotesk', sans-serif;
            margin-bottom: 6px;
        }

        .subtle-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            background: var(--muted-chip);
            color: #0b78c7;
            border: 1px solid #b9dcfb;
            padding: 6px 10px;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .custom-checkbox-card input:checked+label {
            border-color: var(--primary-dark) !important;
            background-color: rgba(13, 110, 253, 0.05);
        }

        .custom-checkbox-card input:disabled+label {
            opacity: 0.7;
            cursor: not-allowed;
            background-color: #f8f9fc;
        }

        .pointer {
            cursor: pointer;
        }

        .btn-pay:hover {
            transform: translateY(-2px);
            filter: brightness(1.04);
        }

        .package-summary {
            border-radius: 16px;
            border: 1px solid #b9dcfb;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: #fff;
            padding: 16px;
        }

        .summary-label {
            font-size: .82rem;
            opacity: .85;
        }

        .summary-value {
            font-size: 1.7rem;
            font-weight: 800;
            margin-bottom: 0;
            font-family: 'Space Grotesk', sans-serif;
        }

        .selected-summary {
            border: 1px solid #d7e9fb;
            border-radius: 14px;
            background: #f9fcff;
            padding: 12px;
            margin-bottom: 12px;
        }

        .selected-summary h6 {
            font-size: .85rem;
            font-weight: 800;
            margin-bottom: 8px;
            color: #1c4b73;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .selected-item {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-size: .85rem;
            color: #385872;
            padding: 4px 0;
            border-bottom: 1px dashed #d8e6f3;
        }

        .selected-item:last-child {
            border-bottom: 0;
        }

        .selected-item strong {
            color: #132d44;
        }

        .config-card {
            padding: 16px;
            border: 1px solid #d7e9fb;
            border-radius: 14px;
            background: #fbfdff;
        }

        .field-title {
            font-size: .78rem;
            font-weight: 700;
            color: #5f7380;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .pay-cta {
            min-height: 46px;
        }
    </style>

    <div class="container-fluid pb-5 payments-shell">
        <div class="payments-hero mb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                <div>
                    <span class="subtle-chip mb-2"><i class="fa-solid fa-wallet"></i> Billing Akses Group</span>
                    <h4 class="fw-bold mb-1 text-primary">Pembelian Akses Group</h4>
                    <p class="mb-0 text-muted">Atur dan beli Akses Group kamu dari halaman ini.</p>
                </div>
                <a href="/paymentlogs" class="btn btn-outline-secondary rounded-pill">Riwayat Pembayaran</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="section-card p-4">
                    @if ($subscription)
                        <div class="p-3 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 mb-3">
                            <small class="fw-bold text-success d-block mb-1">Akses Group aktif</small>
                            <small class="text-dark">Berlaku sampai {{ $subscription->expires_at->format('d M Y') }}</small>
                        </div>
                    @else
                        <div class="p-3 rounded-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 mb-3">
                            <small class="fw-bold text-warning d-block mb-1">Belum ada Akses Group aktif</small>
                            <small class="text-dark">Selesaikan pembayaran untuk membuka akses create/join group
                                penuh.</small>
                        </div>
                    @endif

                    <h6 class="fw-bold mb-3">Konfigurasi Akses Group</h6>

                    <div class="config-card mb-3">
                        <label class="field-title">Pilih Platform Bot</label>
                        <div class="d-flex flex-column gap-2">
                            @foreach (['whatsapp' => ['fa-whatsapp', 'text-success', 'chk_wa'], 'discord' => ['fa-discord', 'text-primary', 'chk_discord'], 'telegram' => ['fa-telegram', 'text-info', 'chk_telegram']] as $key => $info)
                                <div class="custom-checkbox-card">
                                    <input class="form-check-input d-none" type="checkbox" id="{{ $info[2] }}"
                                        onchange="calculatePrice()" {{ $subscription?->{'has_' . $key} ? 'checked' : '' }}>
                                    <label
                                        class="d-flex align-items-center justify-content-between p-2 border rounded-3 pointer mb-0"
                                        for="{{ $info[2] }}">
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="fab {{ $info[0] }} {{ $info[1] }}"></i>
                                            <span class="fw-bold text-dark small">{{ ucfirst($key) }}</span>
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="field-title">Durasi (bulan)</label>
                            <div class="input-group rounded-pill overflow-hidden border bg-white">
                                <button class="btn btn-light border-0" onclick="changeValue('input_duration', -1)"><i
                                        class="fas fa-minus small"></i></button>
                                <input type="number" id="input_duration" class="form-control border-0 text-center fw-bold"
                                    value="{{ $subscription?->duration_months ?? 6 }}" min="1" max="24"
                                    onchange="calculatePrice()">
                                <button class="btn btn-light border-0" onclick="changeValue('input_duration', 1)"><i
                                        class="fas fa-plus small"></i></button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="field-title">Maksimal Member</label>
                            <div class="input-group rounded-pill overflow-hidden border bg-white">
                                <button class="btn btn-light border-0" onclick="changeValue('input_members', -5)"><i
                                        class="fas fa-minus small"></i></button>
                                <input type="number" id="input_members" class="form-control border-0 text-center fw-bold"
                                    value="{{ $subscription ? $subscription->max_members : 10 }}"
                                    min="{{ $subscription ? $subscription->max_members : 5 }}" max="500"
                                    step="5" onchange="calculatePrice()">
                                <button class="btn btn-light border-0" onclick="changeValue('input_members', 5)"><i
                                        class="fas fa-plus small"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="section-card p-3 sticky-top" style="top: 85px;">
                    <div class="selected-summary">
                        <h6>Rincian Pilihan</h6>
                        <div class="selected-item">
                            <span>Platform Bot</span>
                            <strong id="selectedBots">-</strong>
                        </div>
                        <div class="selected-item">
                            <span>Durasi</span>
                            <strong><span id="selectedDuration">{{ $subscription?->duration_months ?? 6 }}</span>
                                bulan</strong>
                        </div>
                        <div class="selected-item">
                            <span>Maksimal Member</span>
                            <strong id="selectedMembers">{{ $subscription ? $subscription->max_members : 10 }}</strong>
                        </div>
                        <div class="selected-item">
                            <span>Subtotal</span>
                            <strong id="subtotalPrice">Rp 0</strong>
                        </div>
                        <div class="selected-item">
                            <span>Pajak 10%</span>
                            <strong id="taxPrice">Rp 0</strong>
                        </div>
                    </div>

                    <div class="package-summary mb-3">
                        <p class="summary-label mb-1">Total Estimasi (<span
                                id="durationLabel">{{ $subscription?->duration_months ?? 6 }}</span> Bulan)</p>
                        <h4 class="summary-value" id="totalPrice">Rp 0</h4>
                        <small class="opacity-75">Subtotal + pajak 10% dihitung proporsional terhadap durasi dan
                            fitur.</small>
                    </div>

                    <button class="btn btn-primary w-100 fw-bold rounded-pill btn-pay pay-cta" onclick="pay()">
                        <i class="fa fa-bolt me-2"></i>
                        Beli Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="toast" class="toast align-items-center text-white border-0 shadow-lg" role="alert"
            style="border-radius: 12px;">
            <div class="d-flex">
                <div class="toast-body fw-bold" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script>
        function showToast(message, type = 'danger') {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMessage');
            toast.className = `toast align-items-center text-white border-0 shadow-lg bg-${type}`;
            toastMsg.innerText = message;
            new bootstrap.Toast(toast, {
                delay: 3000
            }).show();
        }

        const pricing = {
            whatsapp: {{ $pricing['whatsapp'] }},
            discord: {{ $pricing['discord'] }},
            telegram: {{ $pricing['telegram'] }},
            per_group: {{ $pricing['per_group'] }},
            per_member: {{ $pricing['per_member'] }},
        };

        function prefillFromQuery() {
            const params = new URLSearchParams(window.location.search);
            if (!params.has('from_dashboard')) {
                return;
            }

            const setChecked = (id, key) => {
                const el = document.getElementById(id);
                if (!el) return;
                if (el.disabled) return;
                el.checked = params.get(key) === '1';
            };

            setChecked('chk_wa', 'has_whatsapp');
            setChecked('chk_discord', 'has_discord');
            setChecked('chk_telegram', 'has_telegram');

            const setValue = (id, key, min, max) => {
                const el = document.getElementById(id);
                if (!el || !params.has(key)) return;
                let value = parseInt(params.get(key), 10);
                if (Number.isNaN(value)) return;
                value = Math.max(min, Math.min(value, max));
                el.value = value;
            };

            setValue('input_duration', 'duration_months', 1, 24);
            setValue('input_members', 'max_members', 2, 500);
        }

        function calculatePrice() {
            let packageCostFor6Months = 0;

            const hasWa = document.getElementById('chk_wa')?.checked ?? false;
            const hasDiscord = document.getElementById('chk_discord')?.checked ?? false;
            const hasTelegram = document.getElementById('chk_telegram')?.checked ?? false;

            const selectedBots = [];
            if (hasWa) selectedBots.push('WhatsApp');
            if (hasDiscord) selectedBots.push('Discord');
            if (hasTelegram) selectedBots.push('Telegram');
            const selectedBotsEl = document.getElementById('selectedBots');
            if (selectedBotsEl) selectedBotsEl.innerText = selectedBots.length ? selectedBots.join(', ') : '-';

            if (hasWa) packageCostFor6Months += pricing.whatsapp;
            if (hasDiscord) packageCostFor6Months += pricing.discord;
            if (hasTelegram) packageCostFor6Months += pricing.telegram;

            const groups = 1; // Fixed: 1 group per purchase
            const members = parseInt(document.getElementById('input_members')?.value) || 10;
            const durationInput = document.getElementById('input_duration');
            let duration = parseInt(durationInput?.value) || 6;
            duration = Math.max(1, Math.min(duration, 24));
            if (durationInput) durationInput.value = duration;
            const durationLabel = document.getElementById('durationLabel');
            if (durationLabel) durationLabel.innerText = duration;
            const selectedDuration = document.getElementById('selectedDuration');
            if (selectedDuration) selectedDuration.innerText = duration;
            packageCostFor6Months += groups * pricing.per_group;
            packageCostFor6Months += members * pricing.per_member;
            const subtotal = Math.round((packageCostFor6Months / 6) * duration);
            const tax = Math.round(subtotal * 0.10);
            const total = subtotal + tax;

            const selectedMembers = document.getElementById('selectedMembers');
            if (selectedMembers) selectedMembers.innerText = members;

            const subtotalEl = document.getElementById('subtotalPrice');
            if (subtotalEl) subtotalEl.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
            const taxEl = document.getElementById('taxPrice');
            if (taxEl) taxEl.innerText = 'Rp ' + tax.toLocaleString('id-ID');

            document.getElementById('totalPrice').innerText = 'Rp ' + total.toLocaleString('id-ID');
            return total;
        }

        function changeValue(id, delta) {
            const input = document.getElementById(id);
            const newVal = parseInt(input.value) + delta;
            if (newVal >= parseInt(input.min) && newVal <= parseInt(input.max)) {
                input.value = newVal;
                calculatePrice();
            }
        }

        function pay() {
            const hasWa = document.getElementById('chk_wa')?.checked ?? false;
            const hasDiscord = document.getElementById('chk_discord')?.checked ?? false;
            const hasTelegram = document.getElementById('chk_telegram')?.checked ?? false;
            const maxGroups = 1; // Fixed: 1 group per purchase
            const maxMembers = document.getElementById('input_members')?.value ?? 10;
            const durationMonths = document.getElementById('input_duration')?.value ?? 6;

            if (!hasWa && !hasDiscord && !hasTelegram) {
                showToast('Pilih minimal 1 bot notifikasi!', 'warning');
                return;
            }

            if (typeof snap === 'undefined' || !snap || typeof snap.pay !== 'function') {
                showToast('Snap Midtrans belum siap. Muat ulang halaman lalu coba lagi.', 'danger');
                return;
            }

            const payButton = document.querySelector('button[onclick="pay()"]');
            if (payButton) {
                payButton.disabled = true;
                payButton.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Memproses...';
            }

            fetch('/payment/snap-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        has_whatsapp: hasWa,
                        has_discord: hasDiscord,
                        has_telegram: hasTelegram,
                        max_groups: maxGroups,
                        max_members: maxMembers,
                        duration_months: durationMonths,
                    })
                })
                .then(async (res) => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        throw new Error(data.error || 'Gagal membuat token pembayaran.');
                    }
                    return data;
                })
                .then(data => {
                    if (data.error) {
                        showToast(data.error, 'danger');
                        if (payButton) {
                            payButton.disabled = false;
                            payButton.innerHTML = '<i class="fa fa-bolt me-2"></i>Beli Sekarang';
                        }
                        return;
                    }

                    if (!data.token) {
                        showToast('Token pembayaran tidak ditemukan.', 'danger');
                        if (payButton) {
                            payButton.disabled = false;
                            payButton.innerHTML = '<i class="fa fa-bolt me-2"></i>Beli Sekarang';
                        }
                        return;
                    }

                    snap.pay(data.token, {
                        onSuccess: function(result) {
                            fetch('/payment/sync-bots', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        order_id: result.order_id
                                    })
                                })
                                .then(res => res.json())
                                .then(() => {
                                    window.location.href = '/payment/receipt/' + result.order_id;
                                });
                        },
                        onPending: function() {
                            showToast('Menunggu pembayaran...', 'warning');
                            if (payButton) {
                                payButton.disabled = false;
                                payButton.innerHTML = '<i class="fa fa-bolt me-2"></i>Beli Sekarang';
                            }
                        },
                        onError: function() {
                            showToast('Pembayaran gagal!', 'danger');
                            if (payButton) {
                                payButton.disabled = false;
                                payButton.innerHTML = '<i class="fa fa-bolt me-2"></i>Beli Sekarang';
                            }
                        },
                        onClose: function() {
                            if (payButton) {
                                payButton.disabled = false;
                                payButton.innerHTML = '<i class="fa fa-bolt me-2"></i>Beli Sekarang';
                            }
                            fetch('/payment/check-pending', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.synced) {
                                        window.location.href = '/payment/receipt/' + data.order_id;
                                    }
                                });
                        }
                    });
                })
                .catch((err) => {
                    showToast(err.message || 'Terjadi kendala saat memproses pembayaran.', 'danger');
                    if (payButton) {
                        payButton.disabled = false;
                        payButton.innerHTML = '<i class="fa fa-bolt me-2"></i>Beli Sekarang';
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            prefillFromQuery();
            calculatePrice();

            fetch('/payment/check-pending', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.synced) {
                        window.location.href = '/payment/receipt/' + data.order_id;
                    }
                })
                .catch(() => {});
        });
    </script>
@endsection
