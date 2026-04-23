@extends('layout.cdn')

@section('content2')
    <div class="min-vh-100 d-flex align-items-center"
        style="background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="container py-5">
            <div class="row justify-content-center align-items-center g-5">

                {{-- SISI KIRI: Value Proposition (Mengapa Daftar?) --}}
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="pe-lg-5">
                        <a href="{{ route('landing') }}"
                            class="d-flex align-items-center gap-2 fw-bold text-dark text-decoration-none mb-5">
                            <img src="{{ asset('logos/logo_transparan.png') }}" alt="Tasku"
                                style="width: 40px; height: 40px; object-fit: contain;">
                            <span class="fs-4" style="letter-spacing: -1px;">Tasku</span>
                        </a>

                        <span
                            class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold mb-4 shadow-xs">
                            <i class="fa-solid fa-rocket me-1"></i> DAFTAR GRATIS
                        </span>
                        <h1 class="fw-800 text-dark mb-4 display-5" style="letter-spacing: -2px; line-height: 1.1;">
                            Bangun Ruang Kerja <br> Digital Tim Anda.
                        </h1>
                        <p class="text-secondary fs-6 mb-5 lh-lg">
                            Daftar sekarang untuk mulai mengirim pengumuman otomatis, mengelola anggota, dan meningkatkan
                            produktivitas tim dalam satu aplikasi.
                        </p>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="p-3 bg-white rounded-4 shadow-xs border h-100">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary d-inline-block mb-2"><i
                                            class="fa-solid fa-check"></i></div>
                                    <div class="fw-bold text-dark small">Setup Cepat</div>
                                    <div class="text-muted" style="font-size: 11px;">Kurang dari 2 menit</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 bg-white rounded-4 shadow-xs border h-100">
                                    <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success d-inline-block mb-2"><i
                                            class="fa-solid fa-bell"></i></div>
                                    <div class="fw-bold text-dark small">Smart Broadcast</div>
                                    <div class="text-muted" style="font-size: 11px;">WA, Discord, Telegram</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SISI KANAN: Form Register --}}
                <div class="col-12 col-md-10 col-lg-5">
                    <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                        <div class="card-body p-4 p-md-5 bg-white">

                            {{-- Mobile Logo --}}
                            <div class="text-center d-lg-none mb-4">
                                <img src="{{ asset('logos/tasku_transparan_dengan_nama.png') }}" alt="Tasku"
                                    style="height: 42px; width: auto; object-fit: contain;" class="mb-2">
                            </div>

                            <div class="mb-4 text-center text-lg-start">
                                <h4 class="fw-bold text-dark mb-1">Buat Akun</h4>
                                <p class="text-muted small">Lengkapi data di bawah untuk memulai.</p>
                            </div>

                            {{-- ERROR HANDLING --}}
                            @if ($errors->any())
                                <div class="alert alert-danger border-0 rounded-3 py-2 mb-4">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li class="small fw-medium">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('register.auth') }}" method="POST">
                                @csrf

                                {{-- Nama --}}
                                <div class="mb-3">
                                    <label
                                        class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Nama
                                        Lengkap</label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-light border-2 border-end-0 rounded-start-3 text-muted">
                                            <i class="fa-solid fa-user"></i>
                                        </span>
                                        <input type="text" name="name"
                                            class="form-control form-control-lg border-2 border-start-0 rounded-end-3 fs-6 @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" placeholder="Contoh: Giffa">
                                    </div>
                                </div>

                                {{-- Email --}}
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
                                            value="{{ old('email') }}" placeholder="nama@email.com">
                                    </div>
                                </div>

                                {{-- Nomor WA --}}
                                <div class="mb-3">
                                    <label
                                        class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Nomor
                                        WhatsApp</label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-light border-2 border-end-0 rounded-start-3 text-muted">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </span>
                                        <input type="text" name="phone"
                                            class="form-control form-control-lg border-2 border-start-0 rounded-end-3 fs-6 @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') }}" placeholder="62812xxx">
                                    </div>
                                    <small class="text-muted" style="font-size: 10px;">Gunakan kode negara (62)</small>
                                </div>

                                {{-- Password --}}
                                <div class="row g-3 mb-4">
                                    <div class="col-sm-6">
                                        <label
                                            class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Sandi</label>
                                        <input type="password" name="password"
                                            class="form-control form-control-lg border-2 rounded-3 fs-6 @error('password') is-invalid @enderror"
                                            placeholder="••••••">
                                    </div>
                                    <div class="col-sm-6">
                                        <label
                                            class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Ulangi</label>
                                        <input type="password" name="password_confirmation"
                                            class="form-control form-control-lg border-2 rounded-3 fs-6"
                                            placeholder="••••••">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm mb-4"
                                    style="background: #1e293b; border: none;">
                                    Daftar Sekarang <i class="fa-solid fa-check-circle ms-2"></i>
                                </button>
                            </form>

                            <div class="text-center">
                                <p class="small text-muted mb-0">Sudah memiliki akun?
                                    <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Masuk
                                        ke Akun</a>
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
