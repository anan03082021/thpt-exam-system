<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'GV Portal' }}</title>
    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    
    {{-- NAVBAR GIÁO VIÊN --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('teacher.dashboard') }}">Hệ thống thi (GV)</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#teacherNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="teacherNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    {{-- GỌI FILE MENU --}}
                    {{-- Lưu ý: Dựa trên hình ảnh bạn gửi, file menu nằm ở layouts/menus/teacher.blade.php --}}
                    @include('layouts.menus.teacher')
                </ul>

                <div class="d-flex text-white align-items-center gap-2">
                    <span>{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-light text-primary fw-bold">Thoát</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- NỘI DUNG CHÍNH --}}
    <div class="container">
        {{ $slot }}
    </div>

</body>
</html>