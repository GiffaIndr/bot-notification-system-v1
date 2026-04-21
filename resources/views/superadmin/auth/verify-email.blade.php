@extends('superadmin.layout')

@section('title', 'Verify Email')
@section('page_title', 'Email Verification Required')

@section('css')
    <style>
        .verify-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .verify-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .verify-icon {
            font-size: 48px;
            color: #f59e0b;
            margin-bottom: 20px;
        }

        .verify-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .verify-text {
            color: #64748b;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .alert-info {
            background: #f0f9ff;
            border: 1px solid #e0f2fe;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="verify-container">
        <div class="verify-card">
            <div class="verify-icon">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <h2 class="verify-title">Email Verification Required</h2>
            <p class="verify-text">
                Kami telah mengirimkan link verifikasi ke email Anda.
                Silakan cek inbox Anda dan klik link untuk memverifikasi email.
            </p>

            <div class="alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Tip:</strong> Periksa folder "Spam" jika email tidak terlihat di inbox utama.
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('superadmin.verification.send') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-redo me-2"></i>Resend Verification Email
                </button>
            </form>

            <hr style="margin: 20px 0;">

            <form action="{{ route('superadmin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>
@endsection
