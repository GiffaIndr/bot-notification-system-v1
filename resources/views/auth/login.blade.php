@extends('layout.cdn')

@section('content2')
    <div class="min-vh-100 d-flex align-items-center"
        style="background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="container py-5">
            <div class="row justify-content-center align-items-center g-5">

                {{-- SISI KIRI: Branding & Value Proposition --}}
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="pe-lg-5">
                        <a href="{{ route('landing') }}"
                            class="d-flex align-items-center gap-2 fw-bold text-dark text-decoration-none mb-5">
                            <img src="{{ asset('logos/logo_transparan.png') }}" alt="Tasku"
                                style="width: 40px; height: 40px; object-fit: contain;">
                            <span class="fs-4" style="letter-spacing: -1px;">Tasku</span>
                        </a>

                        <span
                            class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-4 shadow-xs">
                            <i class="fa-solid fa-shield-check me-1"></i> AKSES AMAN
                        </span>
                        <h1 class="fw-800 text-dark mb-4 display-5" style="letter-spacing: -2px; line-height: 1.1;">
                            Kelola Notifikasi Tim <br> dalam Satu Pintu.
                        </h1>
                        <p class="text-secondary fs-6 mb-5 lh-lg">
                            Masuk untuk melanjutkan pengelolaan pengumuman, integrasi bot, dan koordinasi grup kerja Anda.
                        </p>

                        <div class="d-grid gap-3">
                            <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-4 shadow-xs border">
                                <div class="bg-light p-2 rounded-3 text-primary"><i class="fa-solid fa-bolt-lightning"></i>
                                </div>
                                <span class="fw-semibold text-dark small">Koneksi Real-time ke WA & Discord</span>
                            </div>
                            <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-4 shadow-xs border">
                                <div class="bg-light p-2 rounded-3 text-success"><i
                                        class="fa-solid fa-clock-rotate-left"></i></div>
                                <span class="fw-semibold text-dark small">Log Aktivitas & Audit Trail Lengkap</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SISI KANAN: Form Login --}}
                <div class="col-12 col-md-8 col-lg-5">
                    <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                        <div class="card-body p-4 p-md-5 bg-white">

                            {{-- Mobile Logo --}}
                            <div class="text-center d-lg-none mb-4">
                                <img src="{{ asset('logos/tasku_transparan_dengan_nama.png') }}" alt="Tasku"
                                    style="height: 42px; width: auto; object-fit: contain;" class="mb-2">
                            </div>

                            <div class="mb-4 text-center text-lg-start">
                                <h4 class="fw-bold text-dark mb-1">Selamat Datang</h4>
                                <p class="text-muted small">Silakan masuk dengan kredensial akun Anda.</p>
                            </div>

                            {{-- ALERTS --}}
                            @if (session('success'))
                                <div class="alert alert-success border-0 rounded-3 py-2 small fw-medium mb-4">
                                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                                </div>
                            @endif

                            @if (session('failed'))
                                <div class="alert alert-danger border-0 rounded-3 py-2 small fw-medium mb-4">
                                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('failed') }}
                                </div>
                            @endif

                            <form action="{{ route('auth') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label
                                        class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Alamat
                                        Email</label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-light border-2 border-end-0 rounded-start-3 text-muted">
                                            <i class="fa-solid fa-envelope"></i>
                                        </span>
                                        <input type="email" name="email"
                                            class="form-control form-control-lg border-2 border-start-0 rounded-end-3 fs-6 @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" placeholder="nama@perusahaan.com">
                                    </div>
                                    @error('email')
                                        <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label
                                            class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Kata
                                            Sandi</label>
                                        {{-- <a href="#" class="small text-decoration-none fw-bold text-primary">Lupa?</a> --}}
                                    </div>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-light border-2 border-end-0 rounded-start-3 text-muted">
                                            <i class="fa-solid fa-lock"></i>
                                        </span>
                                        <input type="password" name="password"
                                            class="form-control form-control-lg border-2 border-start-0 rounded-end-3 fs-6 @error('password') is-invalid @enderror"
                                            placeholder="••••••••">
                                    </div>
                                    @error('password')
                                        <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm mb-4"
                                    style="background: #1e293b; border: none;">
                                    Masuk ke Dashboard <i class="fa-solid fa-arrow-right ms-2"></i>
                                </button>
                            </form>

                            <div class="text-center">
                                <p class="small text-muted mb-0">Belum punya akun?
                                    <a href="{{ route('register') }}"
                                        class="text-primary fw-bold text-decoration-none">Daftar Sekarang</a>
                                </p>
                                <hr class="my-4 opacity-25">
                                <a href="{{ route('landing') }}"
                                    class="small text-secondary fw-semibold text-decoration-none">
                                    <i class="fa-solid fa-chevron-left me-1" style="font-size: 10px;"></i> Kembali ke
                                    Beranda
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <small class="text-muted">&copy; 2026 Tasku Platform. Versi 1.0.4</small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .fw-800 {
            font-weight: 800;
        }

        .shadow-xs {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .rounded-5 {
            border-radius: 2rem !important;
        }

        .rounded-start-3 {
            border-top-left-radius: 0.75rem !important;
            border-bottom-left-radius: 0.75rem !important;
        }

        .rounded-end-3 {
            border-top-right-radius: 0.75rem !important;
            border-bottom-right-radius: 0.75rem !important;
        }

        .form-control:focus {
            border-color: var(--tasku-primary);
            box-shadow: none;
            background-color: #fff;
        }

        .input-group-text {
            transition: border-color 0.2s;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--tasku-primary);
        }
    </style>
@endsection
