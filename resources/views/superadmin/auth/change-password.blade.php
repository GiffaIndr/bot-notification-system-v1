@extends('superadmin.layout')

@section('title', 'Change Password')
@section('page_title', 'Change Password')

@section('css')
    <style>
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 8px;
            font-size: 14px;
            display: block;
        }

        .form-control {
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            width: 100%;
        }

        .form-control:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
        }

        .invalid-feedback {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .password-requirements {
            background: #f0f9ff;
            border: 1px solid #e0f2fe;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #0c4a6e;
        }

        .password-requirements h6 {
            margin-bottom: 10px;
            color: #0c4a6e;
            font-weight: 600;
        }

        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
        }

        .password-requirements li {
            margin-bottom: 5px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-submit {
            background: #7c3aed;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
        }

        .btn-submit:hover {
            background: #6d28d9;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            border-color: #cbd5e1;
            color: #1a202c;
        }

        .security-note {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 12px;
            border-radius: 4px;
            font-size: 12px;
            color: #991b1b;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="form-card">
        <h5 class="mb-4"><i class="fas fa-lock me-2"></i>Change Password</h5>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="security-note">
            <i class="fas fa-shield-alt me-2"></i>
            <strong>Security:</strong> Choose a strong password and don't share it with anyone.
        </div>

        <form action="{{ route('superadmin.change-password.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="current_password">Current Password *</label>
                <input type="password" id="current_password" name="current_password"
                    class="form-control @error('current_password') is-invalid @enderror"
                    placeholder="Enter your current password" required>
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="password-requirements">
                <h6><i class="fas fa-info-circle me-2"></i>Password Requirements</h6>
                <ul>
                    <li>At least 8 characters long</li>
                    <li>At least one uppercase letter (A-Z)</li>
                    <li>At least one lowercase letter (a-z)</li>
                    <li>At least one number (0-9)</li>
                    <li>At least one special character (@, $, !, %, *, ?, &)</li>
                </ul>
            </div>

            <div class="form-group">
                <label for="password">New Password *</label>
                <input type="password" id="password" name="password"
                    class="form-control @error('password') is-invalid @enderror" placeholder="Enter your new password"
                    required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password *</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    placeholder="Confirm your new password" required>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="button-group">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save me-2"></i>Update Password
                </button>
                <a href="{{ route('superadmin.dashboard') }}" class="btn-cancel">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
