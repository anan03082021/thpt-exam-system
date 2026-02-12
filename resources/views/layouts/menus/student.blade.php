{{-- 1. KỲ THI (Dashboard) --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
       href="{{ route('dashboard') }}">
        <i class="bi bi-grid-fill"></i>
        <span>{{ __('Kỳ thi') }}</span>
    </a>
</li>

{{-- 2. ĐỀ THI (Luyện tập) --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('student.practice') ? 'active' : '' }}" 
       href="{{ route('student.practice') }}">
        <i class="bi bi-journal-text"></i>
        <span>{{ __('Ôn luyện') }}</span>
    </a>
</li>

{{-- 3. LỊCH SỬ LÀM BÀI --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('student.history') ? 'active' : '' }}" 
       href="{{ route('student.history') }}">
        <i class="bi bi-clock-history"></i>
        <span>{{ __('Tiến độ học tập') }}</span>
    </a>
</li>

{{-- 4. TÀI LIỆU --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('student.documents') ? 'active' : '' }}" 
       href="{{ route('student.documents') }}">
        <i class="bi bi-file-earmark-arrow-down-fill"></i>
        <span>{{ __('Tài liệu') }}</span>
    </a>
</li>