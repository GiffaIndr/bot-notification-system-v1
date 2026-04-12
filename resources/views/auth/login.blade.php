@extends('layout.cdn')

@section('content2')
    <style>
        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background:
                radial-gradient(circle at 6% 6%, #c8ebff 0, transparent 34%),
                radial-gradient(circle at 92% 15%, #ffe6c8 0, transparent 24%),
                radial-gradient(circle at 80% 86%, #bde3ff 0, transparent 30%),
                #eef8ff;
        }

        .auth-container {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
        }

        .btn-auth {
            border: 0;
            border-radius: 999px;
            text-decoration: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.92rem;
            padding: 10px 16px;
            color: #fff;
            background: linear-gradient(145deg, #00b7ff, #0096ff);
            box-shadow: 0 10px 24px rgba(0, 151, 255, 0.35);
        }

        .auth-main {
            width: 100%;
            padding: 24px 0;
        }

        .auth-copy h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2rem, 5vw, 3rem);
            line-height: 1.04;
            margin-bottom: 0.9rem;
            color: #102022;
            letter-spacing: -0.02em;
        }

        .auth-copy p {
            color: #567078;
            line-height: 1.7;
            max-width: 52ch;
        }

        .auth-list {
            display: grid;
            gap: 10px;
            margin-top: 18px;
        }

        .auth-list div {
            border-radius: 12px;
            padding: 10px 12px;
            background: rgba(255, 255, 255, 0.66);
            border: 1px solid #d8e8df;
            color: #1e3a35;
            font-size: 0.92rem;
            font-weight: 600;
        }

        .auth-form-wrap {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid #d8e8df;
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(15, 50, 42, 0.08);
            padding: 24px;
        }

        .auth-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            background: #e6f4ff;
            color: #0b78c7;
            border: 1px solid #b9dcfb;
            padding: 7px 12px;
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 10px;
        }

        .auth-title {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .auth-subtitle {
            color: #6b7280;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px 12px;
        }

        .btn-main {
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 700;
            background: linear-gradient(145deg, #00b7ff, #0096ff);
            border: 0;
        }

        .auth-link {
            color: #0096ff;
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 991px) {
            .auth-main {
                padding: 16px 0;
            }

            .auth-form-wrap {
                padding: 20px;
            }
        }
    </style>

    <div class="auth-page">
        <section class="auth-main">
            <div class="auth-container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6 auth-copy">
                        <span class="auth-kicker"><i class="fa-solid fa-sparkles"></i> Masuk ke Tasku</span>
                        <h1>Selamat Datang Kembali</h1>
                        <p>Masuk ke akunmu untuk mengelola pengumuman, polling, lampiran file, dan aktivitas grup di
                            Tasku.</p>
                        <div class="auth-list">
                            <div><i class="fa-solid fa-check me-2"></i> Kelola announcement lebih cepat</div>
                            <div><i class="fa-solid fa-check me-2"></i> Atur role dan permission tim</div>
                            <div><i class="fa-solid fa-check me-2"></i> Pantau aktivitas dalam satu tempat</div>
                        </div>
                    </div>

                    <div class="col-lg-5 ms-lg-auto">
                        <div class="auth-form-wrap">
                            <h4 class="auth-title">Masuk</h4>
                            <p class="auth-subtitle">Masuk ke akun Tasku kamu</p>

                            @if (session('success'))
                                <div class="alert alert-success py-2">{{ session('success') }}</div>
                            @endif

                            @if (session('failed'))
                                <div class="alert alert-danger py-2">{{ session('failed') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger py-2">
                                    @foreach ($errors->all() as $error)
                                        <div class="small">• {{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif

                            <form action="{{ route('auth') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" placeholder="contoh@email.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Masukkan password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary btn-main w-100">Masuk</button>
                            </form>

                            <div class="text-center mt-3">
                                <small class="text-muted">Belum punya akun?
                                    <a href="{{ route('register') }}" class="auth-link">Daftar</a>
                                </small>
                            </div>

                            <div class="text-center mt-2">
                                <small><a href="{{ route('landing') }}" class="auth-link">Kembali ke Landing
                                        Page</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
