{{-- 1. Dashboard --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" 
       href="{{ route('teacher.dashboard') }}">
        <i class="bi bi-speedometer2"></i> {{ __('Tổng quan') }}
    </a>
</li>

{{-- 2. Ngân hàng câu hỏi --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('teacher.questions.*') ? 'active' : '' }}" 
       href="{{ route('teacher.questions.index') }}">
        <i class="bi bi-collection"></i> {{ __('Ngân hàng câu hỏi') }}
    </a>
</li>

{{-- 3. Quản lý đề thi --}}
{{-- Lưu ý: Menu nên trỏ về danh sách (index) thay vì trang tạo (create) --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('teacher.exams.*') ? 'active' : '' }}" 
       href="{{ route('teacher.exams.index') }}">
        <i class="bi bi-file-earmark-text"></i> {{ __('Quản lý đề thi') }}
    </a>
</li>

{{-- 4. Tổ chức thi (Ca thi) --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('teacher.sessions.*') ? 'active' : '' }}" 
       href="{{ route('teacher.sessions.index') }}">
        <i class="bi bi-calendar-check"></i> {{ __('Tổ chức thi') }}
    </a>
</li>