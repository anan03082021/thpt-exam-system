<x-layouts.admin title="Quản lý tài khoản">
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">👥 Danh sách tài khoản</h5>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary fw-bold">
                <i class="bi bi-person-plus-fill me-1"></i> Tạo tài khoản mới
            </a>
        </div>

        {{-- Bộ lọc --}}
        <div class="card-body bg-light border-bottom">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm tên hoặc email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Tất cả vai trò --</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Học sinh</option>
                        <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Giáo viên</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Lọc</button>
                </div>
            </form>
        </div>

        {{-- Bảng danh sách --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Họ và tên</th>
                        <th>Email (Tài khoản)</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="ps-4">#{{ $user->id }}</td>
                            <td class="fw-bold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'teacher')
                                    <span class="badge bg-primary">Giáo viên</span>
                                @elseif($user->role == 'student')
                                    <span class="badge bg-success">Học sinh</span>
                                @else
                                    <span class="badge bg-secondary">{{ $user->role }}</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa tài khoản này?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-white border-0 py-3">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.admin>