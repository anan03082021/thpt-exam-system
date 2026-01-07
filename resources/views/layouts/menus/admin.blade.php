{{-- resources/views/layouts/menus/admin.blade.php --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
        <i class="bi bi-speedometer2"></i> Tổng quan
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
        <i class="bi bi-people-fill"></i> Quản lý Tài khoản
    </a>
</li>