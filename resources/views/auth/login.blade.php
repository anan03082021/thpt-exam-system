<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Luyện thi THPT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .login-cover {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .login-form-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: white;
        }
        .form-wrapper { width: 100%; max-width: 420px; }
        .form-control-lg { font-size: 0.95rem; padding: 0.8rem 1rem; }
        .btn-primary-lg { padding: 0.8rem; font-weight: 600; font-size: 1rem; }
        .bg-circle {
            position: absolute; background: rgba(255, 255, 255, 0.1); border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="container-fluid g-0">
        <div class="row g-0">
            <div class="col-lg-6 d-none d-lg-block position-relative login-cover">
                <div class="bg-circle" style="width: 300px; height: 300px; top: -50px; left: -50px;"></div>
                <div class="bg-circle" style="width: 200px; height: 200px; bottom: 50px; right: 50px;"></div>
                
                <div class="text-center position-relative z-1 px-5">
                    <div class="mb-4 display-1"><i class="bi bi-mortarboard-fill"></i></div>
                    <h2 class="fw-bold display-6 mb-3">Chào mừng trở lại!</h2>
                    <p class="lead opacity-75">Tiếp tục hành trình chinh phục kỳ thi THPT Quốc gia cùng hàng ngàn học sinh khác.</p>
                </div>
            </div>

            <div class="col-lg-6 login-form-container">
                <div class="form-wrapper">
                    <div class="text-center mb-5">
                        <h3 class="fw-bold text-primary">Đăng nhập</h3>
                        <p class="text-muted">Nhập thông tin tài khoản của bạn</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control form-control-lg border-start-0 ps-0 bg-light" 
                                       placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <label class="form-label fw-bold small text-secondary">Mật khẩu</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="small text-decoration-none">Quên mật khẩu?</a>
                                @endif
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control form-control-lg border-start-0 ps-0 bg-light" 
                                       placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                            <label class="form-check-label small" for="remember_me">Ghi nhớ đăng nhập</label>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-primary-lg rounded-3 shadow-sm">
                                Đăng nhập
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-muted small mb-0">Chưa có tài khoản? 
                                <a href="{{ route('register') }}" class="fw-bold text-primary text-decoration-none">Đăng ký ngay</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>