@extends('layout.sidebar')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">

            <div class="card shadow-sm border-0">

                {{-- Header --}}
                <div class="card-body text-center py-5 border-bottom">
                    <div class="mb-3">
                        <span
                            class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10"
                            style="width: 80px; height: 80px;">
                            <i class="fa fa-circle-check text-success" style="font-size: 2.5rem"></i>
                        </span>
                    </div>
                    <h4 class="fw-bold mb-1">Pembayaran Berhasil!</h4>
                    <p class="text-muted mb-0">Terima kasih, langganan kamu telah aktif.</p>
                </div>

                {{-- Detail --}}
                <div class="card-body">
                    <h6 class="fw-bold text-muted mb-3 small text-uppercase">Detail Pembayaran</h6>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Order ID</span>
                        <span class="fw-semibold small">{{ $payment->order_id }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Jumlah</span>
                        <span class="fw-semibold small">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Tanggal</span>
                        <span class="fw-semibold small">{{ $payment->created_at->format('d M Y, H:i') }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Aktif Hingga</span>
                        <span class="fw-semibold small text-success">
                            {{ $payment->expires_at ? $payment->expires_at->format('d M Y') : ($payment->subscription->expires_at ? $payment->subscription->expires_at->format('d M Y') : '-') }}
                        </span>
                    </div>

                    <hr>

                    {{-- Fitur --}}
                    <h6 class="fw-bold text-muted mb-3 small text-uppercase">Fitur yang Didapat</h6>

                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-2">
                            @if ($payment->subscription->has_whatsapp)
                                <i class="fa fa-circle-check text-success"></i>
                            @else
                                <i class="fa fa-circle-xmark text-danger"></i>
                            @endif
                            <span class="small"><i class="fab fa-whatsapp text-success me-1"></i>WhatsApp Bot</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @if ($payment->subscription->has_discord)
                                <i class="fa fa-circle-check text-success"></i>
                            @else
                                <i class="fa fa-circle-xmark text-danger"></i>
                            @endif
                            <span class="small"><i class="fab fa-discord text-primary me-1"></i>Discord Bot</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @if ($payment->subscription->has_telegram)
                                <i class="fa fa-circle-check text-success"></i>
                            @else
                                <i class="fa fa-circle-xmark text-danger"></i>
                            @endif
                            <span class="small"><i class="fab fa-telegram text-info me-1"></i>Telegram Bot</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-circle-check text-success"></i>
                            <span class="small"><i class="fa fa-layer-group text-secondary me-1"></i>Max
                                {{ $payment->subscription->max_groups }} Group</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-circle-check text-success"></i>
                            <span class="small"><i class="fa fa-users text-secondary me-1"></i>Max
                                {{ $payment->subscription->max_members }} Member per Group</span>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="card-body border-top d-flex gap-2">
                    <a href="/dashboard" class="btn btn-primary w-100">
                        <i class="fa fa-house me-1"></i> Ke Dashboard
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary w-100">
                        <i class="fa fa-print me-1"></i> Print
                    </button>
                </div>

            </div>

        </div>
    </div>
@endsection
