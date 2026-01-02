<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Luyện thi THPT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .register-cover {
            background: linear-gradient(135deg, #198754 0%, #0d6efd 100%); /* Gradient hơi khác để phân biệt */
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .register-form-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: white;
        }
        .form-wrapper { width: 100%; max-width: 450px; }
        .form-control-lg { font-size: 0.95rem; padding: 0.75rem 1rem; }
        .btn-primary-lg { padding: 0.8rem; font-weight: 600; font-size: 1rem; }
        .bg-circle {
            position: absolute; background: rgba(255, 255, 255, 0.1); border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="container-fluid g-0">
        <div class="row g-0">
            <div class="col-lg-6 register-form-container order-2 order-lg-1">
                <div class="form-wrapper">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary">Tạo tài khoản mới</h3>
                        <p class="text-muted">Tham gia ngay để bắt đầu luyện thi miễn phí</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 mb-3">
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Họ và tên</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control form-control-lg border-start-0 ps-0 bg-light" 
                                       placeholder="Nguyễn Văn A" value="{{ old('name') }}" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control form-control-lg border-start-0 ps-0 bg-light" 
                                       placeholder="name@example.com" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-secondary">Mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control form-control-lg border-start-0 ps-0 bg-light" 
                                           placeholder="••••••••" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-secondary">Xác nhận MK</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-check2-square"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control form-control-lg border-start-0 ps-0 bg-light" 
                                           placeholder="••••••••" required>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="role" value="student">

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label small" for="terms">
                                Tôi đồng ý với <a href="#" class="text-decoration-none">Điều khoản sử dụng</a>
                            </label>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-primary-lg rounded-3 shadow-sm">
                                Đăng ký tài khoản
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-muted small mb-0">Đã có tài khoản? 
                                <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none">Đăng nhập</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 d-none d-lg-block position-relative register-cover order-1 order-lg-2">
                <div class="bg-circle" style="width: 400px; height: 400px; bottom: -100px; left: -100px;"></div>
                <div class="bg-circle" style="width: 150px; height: 150px; top: 100px; right: 10%;"></div>
                
                <div class="text-center position-relative z-1 px-5">
                    <h2 class="fw-bold display-6 mb-3">Bắt đầu ngay hôm nay!</h2>
                    <p class="lead opacity-75 mb-4">Tạo tài khoản để truy cập kho đề thi không giới hạn và theo dõi tiến độ học tập của bạn.</p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <div class="bg-white bg-opacity-25 p-3 rounded-3 backdrop-blur">
                            <i class="bi bi-people fs-3"></i>
                            <div class="small fw-bold mt-1">Cộng đồng</div>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded-3 backdrop-blur">
                            <i class="bi bi-graph-up fs-3"></i>
                            <div class="small fw-bold mt-1">Thống kê</div>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded-3 backdrop-blur">
                            <i class="bi bi-laptop fs-3"></i>
                            <div class="small fw-bold mt-1">Linh hoạt</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>