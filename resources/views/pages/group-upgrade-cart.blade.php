@extends('layout.sidebar')

@section('content')
    <div class="container-fluid pb-5 pt-3">
        <div class="row justify-content-center g-4">
            <div class="col-12 col-lg-10">

                <!-- Header -->
                <div class="bg-white p-4 rounded-4 shadow-sm mb-4 border d-flex align-items-center gap-3">
                    <a href="{{ route('groups.show', $group) }}" class="btn btn-light border">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="fs-4 fw-bold mb-0 text-dark">Upgrade Grup: {{ $group->name }}</h2>
                        <p class="small mb-0 mt-1 text-muted">Pilih paket upgrade yang diinginkan dan bayar sekali</p>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Left: Upgrade Options -->
                    <div class="col-lg-8">
                        <form method="POST" action="{{ route('groups.upgrade.checkout', $group) }}" id="upgradeForm" onsubmit="return submitUpgradePayment(event)">
                            @csrf

                            <!-- 1. Extend Duration -->
                            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                                <div class="card-header bg-light p-4 border-bottom">
                                    <h5 class="fw-bold text-dark mb-0">
                                        <i class="fa fa-hourglass-end text-primary me-2"></i>Perpanjang Masa Aktif Grup
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <p class="small text-muted mb-3">Pilih durasi perpanjangan atau masukkan bulan custom
                                    </p>

                                    <!-- Preset Options -->
                                    <div class="mb-3">
                                        <label class="small text-muted d-block mb-2">Pilihan Cepat:</label>
                                        @foreach ($extensionOptions as $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input preset-extend" type="radio"
                                                    name="extend_preset" value="{{ $option['months'] }}"
                                                    id="extend{{ $option['months'] }}">
                                                <label class="form-check-label" for="extend{{ $option['months'] }}">
                                                    {{ $option['label'] }} <span class="text-success">+
                                                        Rp{{ number_format($option['price'], 0, ',', '.') }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Custom Input -->
                                    <div class="mb-0">
                                        <label class="small text-muted d-block mb-2">Atau Masukkan Custom (bulan):</label>
                                        <div class="input-group">
                                            <input type="number" name="extend_duration" class="form-control"
                                                placeholder="Misal: 5" min="0" max="60">
                                            <span class="input-group-text">bulan</span>
                                            <span class="input-group-text" id="extendPrice">+ Rp0</span>
                                        </div>
                                        <small class="text-muted d-block mt-2">Harga:
                                            Rp{{ number_format($extendPrice, 0, ',', '.') }} per bulan. Maksimal 60
                                            bulan.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Add Members -->
                            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                                <div class="card-header bg-light p-4 border-bottom">
                                    <h5 class="fw-bold text-dark mb-0">
                                        <i class="fa fa-user-plus text-info me-2"></i>Tambah Kuota Member
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <p class="small text-muted mb-3">Kuota saat ini: <strong>{{ $currentMembers }} /
                                            {{ $subscription->max_members }}</strong></p>

                                    <!-- Preset Options -->
                                    <div class="mb-3">
                                        <label class="small text-muted d-block mb-2">Pilihan Cepat:</label>
                                        @foreach ($memberOptions as $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input preset-members" type="radio"
                                                    name="add_members_preset" value="{{ $option['slots'] }}"
                                                    id="members{{ $option['slots'] }}">
                                                <label class="form-check-label" for="members{{ $option['slots'] }}">
                                                    {{ $option['label'] }} <span class="text-success">+
                                                        Rp{{ number_format($option['price'], 0, ',', '.') }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Custom Input -->
                                    <div class="mb-0">
                                        <label class="small text-muted d-block mb-2">Atau Masukkan Custom (orang):</label>
                                        <div class="input-group">
                                            <input type="number" name="add_members" class="form-control"
                                                placeholder="Misal: 75" min="0" max="1000">
                                            <span class="input-group-text">orang</span>
                                            <span class="input-group-text" id="membersPrice">+ Rp0</span>
                                        </div>
                                        <small class="text-muted d-block mt-2">Harga:
                                            Rp{{ number_format($memberPrice, 0, ',', '.') }} per 5 orang. Maksimal 1000
                                            orang.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Add Bot -->
                            @if (count($availableBots) > 0)
                                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                                    <div class="card-header bg-light p-4 border-bottom">
                                        <h5 class="fw-bold text-dark mb-0">
                                            <i class="fa fa-robot text-warning me-2"></i>Tambah Bot Integration
                                        </h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <p class="small text-muted mb-3">Bot aktif saat ini: <strong>{{ $activeBots }} /
                                                {{ $subscription->max_bots }}</strong></p>

                                        @foreach ($availableBots as $bot)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input bot-checkbox" type="checkbox"
                                                    name="add_bots[]" value="{{ $bot['type'] }}"
                                                    id="bot{{ $bot['type'] }}">
                                                <label class="form-check-label" for="bot{{ $bot['type'] }}">
                                                    {{ $bot['label'] }} <span class="text-success">+
                                                        Rp{{ number_format($bot['price'], 0, ',', '.') }}</span>
                                                </label>
                                            </div>
                                        @endforeach

                                        @if (count($availableBots) === 0)
                                            <p class="small text-muted mb-0">Semua bot sudah aktif</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                        </form>
                    </div>

                    <!-- Right: Cart Summary -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 sticky-top bg-white" style="top: 20px;">
                            <div class="card-header bg-light p-4 border-bottom">
                                <h5 class="fw-bold text-dark mb-0">
                                    <i class="fa fa-shopping-cart me-2"></i>Ringkasan Pembelian
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <!-- Cart Items -->
                                <div id="cartItems">
                                    <p class="small text-muted text-center py-3">Pilih upgrade untuk melihat ringkasan</p>
                                </div>

                                <!-- Total -->
                                <div class="border-top pt-3 mt-3">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="fw-bold">Total Harga:</span>
                                        <span class="fw-bold text-primary" id="totalPrice"
                                            style="font-size: 1.25rem;">Rp0</span>
                                    </div>
                                </div>

                                <!-- Checkout Button -->
                                <button type="submit" form="upgradeForm"
                                    class="btn btn-primary w-100 fw-bold py-2 mt-3">
                                    <i class="fa fa-credit-card me-2"></i> Lanjut Pembayaran
                                </button>

                                <a href="{{ route('groups.show', $group) }}"
                                    class="btn btn-light border w-100 fw-bold py-2 mt-2">
                                    Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function submitUpgradePayment(event) {
            event.preventDefault();

            const form = event.target;
            const submitButton = form.querySelector('button[type="submit"]');

            if (typeof snap === 'undefined' || !snap || typeof snap.pay !== 'function') {
                alert('Snap Midtrans belum siap. Muat ulang halaman lalu coba lagi.');
                return false;
            }

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Memproses...';
            }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            })
            .then(async (response) => {
                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(data.error || 'Gagal membuat token pembayaran.');
                }

                return data;
            })
            .then((data) => {
                if (!data.token) {
                    throw new Error('Token Midtrans tidak ditemukan.');
                }

                snap.pay(data.token, {
                    onSuccess: function(result) {
                        fetch(`{{ route('groups.upgrade.callback', $group) }}?order_id=${encodeURIComponent(result.order_id)}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        })
                        .then(() => {
                            window.location.href = `{{ route('groups.show', $group) }}`;
                        });
                    },
                    onPending: function() {
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = '<i class="fa fa-credit-card me-2"></i> Lanjut Pembayaran';
                        }
                    },
                    onError: function() {
                        alert('Pembayaran gagal!');
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = '<i class="fa fa-credit-card me-2"></i> Lanjut Pembayaran';
                        }
                    },
                    onClose: function() {
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = '<i class="fa fa-credit-card me-2"></i> Lanjut Pembayaran';
                        }
                    }
                });
            })
            .catch((error) => {
                alert(error.message || 'Gagal memproses pembayaran.');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fa fa-credit-card me-2"></i> Lanjut Pembayaran';
                }
            });

            return false;
        }

        const PRICES = {
            extend: {{ $extendPrice }}, // per bulan
            members: {{ $memberPrice }}, // per 5 orang
            bot: {{ $botPrice }}, // per bot
        };

        function updateCart() {
            let total = 0;
            const cartItems = [];

            // Check extend preset
            const extendPreset = document.querySelector('input[name="extend_preset"]:checked');
            if (extendPreset) {
                const months = parseInt(extendPreset.value);
                const price = months * PRICES.extend;
                if (price > 0) {
                    cartItems.push(
                        `<div class="small mb-2 pb-2 border-bottom"><strong>${months} Bulan Extend</strong><br><span class="text-muted">Rp${price.toLocaleString('id-ID')}</span></div>`
                        );
                    total += price;
                    document.querySelector('input[name="extend_duration"]').value = '';
                }
            } else {
                const extendValue = parseInt(document.querySelector('input[name="extend_duration"]').value) || 0;
                if (extendValue > 0) {
                    const price = extendValue * PRICES.extend;
                    cartItems.push(
                        `<div class="small mb-2 pb-2 border-bottom"><strong>${extendValue} Bulan Extend</strong><br><span class="text-muted">Rp${price.toLocaleString('id-ID')}</span></div>`
                        );
                    total += price;
                    document.querySelector('#extendPrice').textContent = `+ Rp${price.toLocaleString('id-ID')}`;
                }
            }

            // Check members preset
            const membersPreset = document.querySelector('input[name="add_members_preset"]:checked');
            if (membersPreset) {
                const slots = parseInt(membersPreset.value);
                const price = (slots === 5) ? PRICES.members : (slots === 10) ? (PRICES.members * 2) : 0;
                if (price > 0) {
                    cartItems.push(
                        `<div class="small mb-2 pb-2 border-bottom"><strong>+${slots} Member</strong><br><span class="text-muted">Rp${price.toLocaleString('id-ID')}</span></div>`
                        );
                    total += price;
                    document.querySelector('input[name="add_members"]').value = '';
                }
            } else {
                const membersValue = parseInt(document.querySelector('input[name="add_members"]').value) || 0;
                if (membersValue > 0) {
                    const price = Math.ceil(membersValue / 5) * PRICES.members;
                    cartItems.push(
                        `<div class="small mb-2 pb-2 border-bottom"><strong>+${membersValue} Member</strong><br><span class="text-muted">Rp${price.toLocaleString('id-ID')}</span></div>`
                        );
                    total += price;
                    document.querySelector('#membersPrice').textContent = `+ Rp${price.toLocaleString('id-ID')}`;
                }
            }

            // Check bots
            const checkedBots = document.querySelectorAll('input[name="add_bots[]"]:checked');
            checkedBots.forEach(bot => {
                const label = bot.nextElementSibling.textContent.split(' ')[0];
                cartItems.push(
                    `<div class="small mb-2 pb-2 border-bottom"><strong>+ ${label} Bot</strong><br><span class="text-muted">Rp${PRICES.bot.toLocaleString('id-ID')}</span></div>`
                    );
                total += PRICES.bot;
            });

            // Update UI
            const cartItemsDiv = document.getElementById('cartItems');
            if (cartItems.length === 0) {
                cartItemsDiv.innerHTML =
                    '<p class="small text-muted text-center py-3">Pilih upgrade untuk melihat ringkasan</p>';
            } else {
                cartItemsDiv.innerHTML = cartItems.join('');
            }

            document.getElementById('totalPrice').textContent = `Rp${total.toLocaleString('id-ID')}`;
        }

        // Event listeners
        document.querySelectorAll('.preset-extend, .preset-members, .bot-checkbox').forEach(el => {
            el.addEventListener('change', updateCart);
        });

        document.querySelector('input[name="extend_duration"]').addEventListener('input', updateCart);
        document.querySelector('input[name="add_members"]').addEventListener('input', updateCart);

        updateCart();
    </script>
@endsection
