{{-- 1. TỔNG QUAN --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
        <i class="bi bi-speedometer2 me-1"></i> Tổng quan
    </a>
</li>

{{-- 2. QUẢN LÝ TÀI KHOẢN --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
        <i class="bi bi-people-fill me-1"></i> Tài khoản
    </a>
</li>

{{-- 3. DIỄN ĐÀN (NẾU CÓ) --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.forum.*') ? 'active' : '' }}" href="{{ route('admin.forum.index') }}">
        <i class="bi bi-chat-dots-fill me-1"></i> Diễn đàn
    </a>
</li>

{{-- 4. DROPDOWN CHỨC NĂNG GIÁO VIÊN --}}
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ request()->routeIs('teacher.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-mortarboard-fill me-1"></i> Công tác Chuyên môn
    </a>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('teacher.questions.index') }}">
                <i class="bi bi-database-fill text-warning"></i> Ngân hàng câu hỏi
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('teacher.exams.index') }}">
                <i class="bi bi-file-earmark-text-fill text-primary"></i> Quản lý Đề thi
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('teacher.sessions.index') }}">
                <i class="bi bi-broadcast text-success"></i> Tổ chức Ca thi
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="{{ route('teacher.documents.index') }}">
                <i class="bi bi-folder-fill text-info"></i> Kho Tài liệu
            </a>
        </li>
    </ul>
</li>