@extends('layout.cdn')

@section('content2')

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">

            <div class="text-center mb-4">
                <h3 class="fw-bold">Buat Akun Baru</h3>
                <p class="text-muted">Daftar untuk mulai menggunakan layanan</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    @foreach ($errors->all() as $error)
                        <div class="small">• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('register.auth') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama</label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Nama lengkap">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="contoh@email.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor WhatsApp</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}"
                                   placeholder="6287703982394">
                            <small class="text-muted">Awali dengan 62, contoh: 6287703982394</small>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Minimal 6 karakter">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   placeholder="Ulangi password">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Daftar Sekarang
                        </button>

                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">Sudah punya akun?
                    <a href="/" class="text-primary text-decoration-none fw-semibold">Login</a>
                </small>
            </div>

        </div>
    </div>
</div>

@endsection
