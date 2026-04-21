<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header .crown {
            font-size: 40px;
            color: #7c3aed;
            margin-bottom: 10px;
        }

        .login-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 5px;
        }

        .login-header p {
            color: #64748b;
            font-size: 14px;
        }

        .form-control {
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(124, 58, 237, 0.3);
        }

        .alert {
            border: none;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .security-note {
            background: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 12px;
            border-radius: 4px;
            font-size: 12px;
            color: #0c4a6e;
            margin-top: 20px;
        }

        .security-note i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="crown"><i class="fas fa-shield-alt"></i></div>
            <h1>Super Admin</h1>
            <p>Secure Access Portal</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Login Failed!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('superadmin.login.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label"><i class="fas fa-envelope me-2"></i>Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                       placeholder="Enter your email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-lock me-2"></i>Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Enter your password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Login Now
            </button>

            <div class="security-note">
                <i class="fas fa-lock"></i>
                Email verification required after login. Super Admin access is logged and monitored.
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
