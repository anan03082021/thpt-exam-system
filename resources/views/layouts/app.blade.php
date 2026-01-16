<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Luyện thi THPT') }}</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Alpine.js (QUAN TRỌNG: Thêm dòng này để sửa lỗi Tab Lịch sử) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        /* Navbar Styling */
        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 0.8rem 0;
        }
        .nav-link {
            font-weight: 600;
            color: #64748b;
            padding: 8px 16px !important;
            border-radius: 50px;
            transition: all 0.2s;
            margin-right: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #eff6ff;
            color: #0d6efd;
        }
        
        /* Dropdown User */
        .avatar-circle {
            width: 32px; height: 32px;
            background: #0d6efd; color: white;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 0.9rem;
        }
        
        /* Main Content Padding for Fixed Nav */
        main { margin-top: 80px; }
    </style>
    
    {{-- Inject CSS từ các trang con (nếu có) --}}
    @stack('styles')
</head>
<body>

    {{-- NAVIGATION BAR --}}
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="{{ route('dashboard') }}">
                <i class="bi bi-mortarboard-fill me-2 fs-4"></i>
                LUYENTHI<span class="text-dark">THPT</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                {{-- MENU CHÍNH --}}
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    {{-- Gọi file menu --}}
                    @include('layouts.menus.student')
                </ul>

                {{-- USER DROPDOWN --}}
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            {{-- LOGIC HIỂN THỊ AVATAR (Bootstrap) --}}
@if(Auth::user()->avatar)
    {{-- Nếu có ảnh: Hiển thị ảnh --}}
    <img src="/storage/{{ Str::replace('public/', '', Auth::user()->avatar) }}" 
         alt="{{ Auth::user()->name }}" 
         class="rounded-circle border border-white shadow-sm"
         style="width: 32px; height: 32px; object-fit: cover;">
@else
    {{-- Nếu chưa có ảnh: Giữ nguyên vòng tròn chữ cái cũ --}}
    <div class="avatar-circle">
        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
    </div>
@endif
                            <span>{{ Auth::user()->name ?? 'Học sinh' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Hồ sơ</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="container py-4">
        {{ $slot }}
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>