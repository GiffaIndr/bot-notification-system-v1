@extends('layout.cdn')

@section('content2')
    <div class="min-vh-100 d-flex align-items-center"
        style="background-color: var(--tasku-bg); font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-5">
                    <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                        <div class="card-body p-4 p-md-5 bg-white">
                            <div class="text-center mb-4">
                                <img src="{{ asset('logos/tasku_transparan_dengan_nama.png') }}" alt="Tasku"
                                    style="height: 42px; width: auto; object-fit: contain;" class="mb-2">
                                <h4 class="fw-bold text-dark mb-1">Verifikasi Email</h4>
                                <p class="text-muted small mb-0">Masukkan 6 digit kode yang kami kirim ke:</p>
                                <p class="fw-semibold text-dark small mb-0">{{ $email }}</p>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success border-0 rounded-3 py-2 small fw-medium mb-3">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('failed'))
                                <div class="alert alert-danger border-0 rounded-3 py-2 small fw-medium mb-3">
                                    {{ session('failed') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger border-0 rounded-3 py-2 mb-3">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li class="small fw-medium">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('register.verify.submit') }}" method="POST" class="mb-3">
                                @csrf
                                <label class="form-label small fw-bold text-secondary text-uppercase">Kode
                                    Verifikasi</label>
                                <input type="text" name="code"
                                    class="form-control form-control-lg text-center fw-bold mb-3" placeholder="123456"
                                    maxlength="6" inputmode="numeric" value="{{ old('code') }}" required>

                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm">
                                    Verifikasi & Selesaikan Registrasi
                                </button>
                            </form>

                            <form action="{{ route('register.verify.resend') }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary w-100 rounded-3 fw-semibold">
                                    Kirim Ulang Kode
                                </button>
                            </form>

                            <div class="text-center">
                                <a href="{{ route('register') }}"
                                    class="small text-secondary fw-semibold text-decoration-none">
                                    <i class="fa-solid fa-chevron-left me-1" style="font-size: 10px;"></i> Kembali ke Form
                                    Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
