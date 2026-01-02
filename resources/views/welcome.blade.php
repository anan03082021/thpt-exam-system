<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - Hệ thống Luyện thi THPT Quốc gia</title>
    
    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            overflow-x: hidden;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }
        
        /* Các hạt trang trí nền (Background circles) */
        .hero-bg-circle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        .circle-1 { width: 300px; height: 300px; top: -100px; right: -50px; }
        .circle-2 { width: 150px; height: 150px; bottom: 50px; left: 10%; }

        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
        }
        
        .icon-box {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .stat-card {
            border-left: 4px solid #0d6efd;
            background: #f8f9fa;
        }

        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .btn-light-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
        }
        .btn-light-outline:hover {
            background: white;
            color: #0d6efd;
        }
    </style>
</head>
<body>

    {{-- 1. NAVIGATION BAR --}}
    <nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="#">
                <i class="bi bi-mortarboard-fill me-2 fs-4"></i>
                LUYENTHI<span class="text-dark">THPT</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link px-3 fw-semibold" href="#features">Tính năng</a></li>
                    <li class="nav-item"><a class="nav-link px-3 fw-semibold" href="#stats">Thống kê</a></li>
                    
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item ms-2">
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                                    Vào Dashboard <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </li>
                        @else
                            <li class="nav-item ms-2">
                                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold me-2">Đăng nhập</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Đăng ký ngay</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    {{-- 2. HERO SECTION --}}
    <header class="hero-section text-center text-lg-start d-flex align-items-center">
        <div class="hero-bg-circle circle-1"></div>
        <div class="hero-bg-circle circle-2"></div>

        <div class="container position-relative z-1">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill mb-3 fw-bold shadow-sm">
                        🚀 Phiên bản mới 2025
                    </span>
                    <h1 class="display-4 fw-bold mb-3 lh-base">
                        Nền tảng ôn thi Tin học <br>
                        <span class="text-warning">Hiệu quả & Toàn diện</span>
                    </h1>
                    <p class="lead mb-4 text-white-50">
                        Hệ thống ngân hàng câu hỏi phong phú, đề thi bám sát cấu trúc đề minh họa. 
                        Giúp học sinh tự tin chinh phục kỳ thi THPT Quốc gia.
                    </p>
                    <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-primary shadow">
                                Bắt đầu học ngay
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold text-dark shadow">
                                Đăng ký miễn phí
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-light-outline btn-lg rounded-pill px-4 fw-bold">
                                Đăng nhập
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    {{-- Hình minh họa dùng Bootstrap Icons ghép lại (thay vì ảnh thật để tránh lỗi link) --}}
                    <div class="position-relative d-inline-block">
                        <div class="bg-white p-4 rounded-4 shadow-lg" style="transform: rotate(-5deg); max-width: 300px;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success text-white rounded-circle p-2 me-3"><i class="bi bi-check-lg"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Kết quả thi thử</h6>
                                    <small class="text-muted">Vừa xong</small>
                                </div>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 85%"></div>
                            </div>
                            <p class="mb-0 fw-bold text-success">8.5 / 10 Điểm</p>
                        </div>

                        <div class="bg-white p-4 rounded-4 shadow-lg position-absolute" style="bottom: -30px; right: -40px; transform: rotate(5deg); max-width: 300px;">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle p-3 me-3">
                                    <i class="bi bi-trophy-fill fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-dark">Top 10</h5>
                                    <small class="text-muted">Học sinh xuất sắc</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- 3. STATS SECTION --}}
    <section id="stats" class="py-5 bg-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-card p-4 rounded-3 h-100">
                        <h2 class="fw-bold text-primary mb-0">1000+</h2>
                        <p class="text-muted mb-0">Câu hỏi trắc nghiệm</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card p-4 rounded-3 h-100" style="border-color: #ffc107;">
                        <h2 class="fw-bold text-warning mb-0">50+</h2>
                        <p class="text-muted mb-0">Đề thi thử chuẩn cấu trúc</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card p-4 rounded-3 h-100" style="border-color: #198754;">
                        <h2 class="fw-bold text-success mb-0">24/7</h2>
                        <p class="text-muted mb-0">Hệ thống hoạt động ổn định</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. FEATURES SECTION --}}
    <section id="features" class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h6 class="text-primary fw-bold text-uppercase ls-2">Tại sao chọn chúng tôi?</h6>
                <h2 class="fw-bold">Công cụ hỗ trợ ôn tập toàn diện</h2>
            </div>

            <div class="row g-4">
                {{-- Feature 1 --}}
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 shadow-sm bg-white">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-hdd-stack"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Ngân hàng câu hỏi lớn</h4>
                        <p class="text-muted mb-0">
                            Hệ thống câu hỏi đa dạng, được phân loại theo chủ đề, mức độ nhận thức (NB, TH, VD, VDC) giúp học sinh ôn luyện từng phần.
                        </p>
                    </div>
                </div>

                {{-- Feature 2 --}}
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 shadow-sm bg-white">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-stopwatch"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Thi thử như thi thật</h4>
                        <p class="text-muted mb-0">
                            Giao diện làm bài thi mô phỏng chính xác kỳ thi thật. Có đếm ngược thời gian và tự động nộp bài khi hết giờ.
                        </p>
                    </div>
                </div>

                {{-- Feature 3 --}}
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 shadow-sm bg-white">
                        <div class="icon-box bg-success bg-opacity-10 text-success">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Phân tích kết quả</h4>
                        <p class="text-muted mb-0">
                            Xem lại lịch sử làm bài chi tiết. Hệ thống tự động chấm điểm và chỉ ra đáp án đúng/sai ngay sau khi nộp bài.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. CALL TO ACTION --}}
    <section class="py-5 bg-white text-center">
        <div class="container">
            <div class="bg-primary bg-opacity-10 rounded-4 p-5">
                <h2 class="fw-bold mb-3">Sẵn sàng chinh phục điểm 10?</h2>
                <p class="text-muted mb-4 lead">Đăng ký tài khoản ngay hôm nay để truy cập kho đề thi miễn phí.</p>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
                        Vào Dashboard ngay
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
                        Đăng ký tài khoản
                    </a>
                @endauth
            </div>
        </div>
    </section>

    {{-- 6. FOOTER --}}
    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-mortarboard-fill"></i> LUYENTHI<span class="text-white">THPT</span></h5>
                    <p class="text-secondary small">
                        Hệ thống luyện thi trắc nghiệm trực tuyến dành cho học sinh THPT. 
                        Nội dung bám sát chương trình giáo dục phổ thông mới.
                    </p>
                </div>
                <div class="col-md-2 offset-md-1">
                    <h6 class="fw-bold mb-3">Liên kết</h6>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary hover-white">Trang chủ</a></li>
                        <li class="mb-2"><a href="#features" class="text-decoration-none text-secondary hover-white">Tính năng</a></li>
                        <li class="mb-2"><a href="#stats" class="text-decoration-none text-secondary hover-white">Thống kê</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold mb-3">Hỗ trợ</h6>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary hover-white">Hướng dẫn</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary hover-white">Liên hệ</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary hover-white">Điều khoản</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">Liên hệ</h6>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> Hà Nội, Việt Nam</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i> support@luyenthi.com</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i> 0123 456 789</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center text-secondary small">
                &copy; {{ date('Y') }} LuyenThiTHPT. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>