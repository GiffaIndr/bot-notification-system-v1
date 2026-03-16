@extends('layout.cdn')

@section('content')

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">

            <div class="text-center mb-4">
                <h3 class="fw-bold">Selamat Datang!</h3>
                <p class="text-muted">Masuk ke akun kamu</p>
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
                    <form action="{{ route('auth') }}" method="POST">
                        @csrf

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

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Masukkan password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Masuk
                        </button>

                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">Belum punya akun?
                    <a href="/register" class="text-primary text-decoration-none fw-semibold">Daftar</a>
                </small>
            </div>

        </div>
    </div>
</div>

@endsection
