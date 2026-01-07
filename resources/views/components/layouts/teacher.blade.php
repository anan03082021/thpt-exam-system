<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Giáo viên | Hệ thống thi' }}</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        
        /* Navbar riêng cho Giáo viên */
        .navbar-teacher { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 0.8rem 0; }
        
        .nav-link { 
            font-weight: 600; color: #555; padding: 8px 16px !important; 
            border-radius: 8px; margin-right: 5px; transition: all 0.2s;
            display: flex; align-items: center; gap: 8px;
        }
        .nav-link:hover, .nav-link.active { background-color: #e0e7ff; color: #4338ca; }
        
        .avatar-circle {
            width: 35px; height: 35px; background: #4338ca; color: white;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 0.9rem;
        }
        main { margin-top: 80px; }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top navbar-teacher">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="{{ route('teacher.dashboard') }}">
                <i class="bi bi-briefcase-fill me-2 fs-4 text-indigo-600"></i>
                <span style="color: #4338ca;">TEACHER</span><span class="text-dark">PORTAL</span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#teacherNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="teacherNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    @include('layouts.menus.teacher')
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <div class="avatar-circle me-2">{{ substr(Auth::user()->name ?? 'G', 0, 1) }}</div>
                            <span>{{ Auth::user()->name ?? 'Giáo viên' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2">
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

    <main class="container py-4">
        {{ $slot }}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>