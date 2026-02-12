<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Portal' }} - Hệ thống thi trắc nghiệm</title>
    
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f3f4f6; font-family: 'Segoe UI', sans-serif; }
        .navbar-admin {
            background: linear-gradient(to right, #1f2937, #111827); /* Màu tối admin */
        }
        /* Style cho Link Menu */
        .navbar-dark .navbar-nav .nav-link { color: rgba(255,255,255,0.75); font-weight: 500; padding: 0.5rem 1rem; }
        .navbar-dark .navbar-nav .nav-link:hover, 
        .navbar-dark .navbar-nav .nav-link.active { color: #fff; background-color: rgba(255,255,255,0.1); border-radius: 6px; }
        
        /* Style cho Dropdown menu con */
        .dropdown-menu { border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-radius: 8px; margin-top: 10px; }
        .dropdown-item { padding: 8px 16px; font-size: 0.95rem; }
        .dropdown-item:hover { background-color: #f3f4f6; color: #2563eb; }
        .dropdown-item i { width: 20px; text-align: center; margin-right: 8px; }
    </style>
    @stack('styles')
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-dark navbar-admin shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-uppercase" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-shield-lock-fill me-2 text-primary"></i> Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                {{-- MENU CHÍNH --}}
                <ul class="navbar-nav me-auto">
                    
                    {{-- [QUAN TRỌNG] GỌI FILE MENU VÀO ĐÂY --}}
                    @include('layouts.menus.admin')

                </ul>

                {{-- User Dropdown (Giữ nguyên của bạn) --}}
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                            <span>{{ Auth::user()->name ?? 'Admin' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="py-4">
        <div class="container">
            {{ $slot }}
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container text-center text-muted small">
            &copy; {{ date('Y') }} Administrator Area.
        </div>
    </footer>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>