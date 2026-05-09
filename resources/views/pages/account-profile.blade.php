@extends('layout.sidebar')

@section('content')
    <div class="container-fluid pb-5 pt-3">
        <div class="row justify-content-center g-4">
            <div class="col-12 col-xl-8">
                <div
                    class="bg-white p-4 rounded-4 shadow-sm mb-4 border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h2 class="fs-4 fw-bold mb-1 text-dark">Akun Saya</h2>
                        <p class="small text-muted mb-0">Edit profil, ganti email, dan ganti password dengan verifikasi OTP
                            email.</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success rounded-4">{{ session('success') }}</div>
                @endif
                @if (session('failed'))
                    <div class="alert alert-danger rounded-4">{{ session('failed') }}</div>
                @endif
                @if (session('info'))
                    <div class="alert alert-info rounded-4">{{ session('info') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger rounded-4 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                    <div class="card-header bg-transparent border-bottom p-4">
                        <h6 class="fw-bold text-dark m-0">Ubah Data Akun</h6>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('account.request-otp') }}" class="row g-3">
                            @csrf
                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted">Nama</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $pendingUpdate['name'] ?? $user->name) }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $pendingUpdate['email'] ?? $user->email) }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted">Nomor WhatsApp (format 62...)</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $pendingUpdate['phone'] ?? $user->phone) }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted">Password Saat Ini (wajib jika ganti
                                    password)</label>
                                <input type="password" name="current_password" class="form-control"
                                    placeholder="Isi jika ingin ganti password">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted">Password Baru</label>
                                <input type="password" name="new_password" class="form-control"
                                    placeholder="Kosongkan jika tidak diganti">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted">Konfirmasi Password Baru</label>
                                <input type="password" name="new_password_confirmation" class="form-control"
                                    placeholder="Ulangi password baru">
                            </div>
                            <div class="col-12 d-grid d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary fw-bold px-4">Kirim OTP Verifikasi</button>
                            </div>
                        </form>
                    </div>
                </div>

                @if ($pendingUpdate && $verification)
                    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                        <div class="card-header bg-transparent border-bottom p-4">
                            <h6 class="fw-bold text-dark m-0">Verifikasi OTP</h6>
                        </div>
                        <div class="card-body p-4">
                            <p class="small text-muted mb-3">
                                OTP dikirim ke email:
                                <strong>{{ $verification['target_email'] ?? $user->email }}</strong>.
                                Masukkan kode untuk menyelesaikan perubahan akun.
                            </p>
                            <form method="POST" action="{{ route('account.verify-otp') }}" class="row g-2 mb-3">
                                @csrf
                                <div class="col-12 col-md-8">
                                    <input type="text" name="code" class="form-control" maxlength="6"
                                        placeholder="Masukkan 6 digit OTP" required>
                                </div>
                                <div class="col-12 col-md-4 d-grid">
                                    <button type="submit" class="btn btn-success fw-bold">Verifikasi & Simpan</button>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('account.resend-otp') }}">
                                @csrf
                                <button type="submit" class="btn btn-light border btn-sm">Kirim Ulang OTP</button>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-header bg-transparent border-bottom p-4">
                        <h6 class="fw-bold text-dark m-0">Keluar Akun</h6>
                    </div>
                    <div class="card-body p-4 d-flex justify-content-end">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger fw-bold">
                                <i class="fas fa-sign-out-alt me-2"></i>Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
