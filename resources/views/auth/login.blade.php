<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống Quản lý Kho</title>
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            width: 100%;
            max-width: 400px;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            border: none;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .card-header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
        }
        .card-body {
            padding: 2rem;
        }
        .input-group-text {
            background: #f8f9fa;
            border-right: none;
        }
        .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .login-box-msg {
            color: #6c757d;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="card">
            <div class="card-header">
                <h1><i class="bi bi-box-seam me-2"></i><b>Hệ thống</b> Quản lý Kho</h1>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Đăng nhập để bắt đầu phiên làm việc</p>
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Mật khẩu" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="form-check">
                                <input type="checkbox" id="remember" name="remember" class="form-check-input">
                                <label for="remember" class="form-check-label">Ghi nhớ đăng nhập</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
                            </button>
                        </div>
                    </div>
                </form>
                <div class="mt-3 text-center text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Demo: admin@example.com / password
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
