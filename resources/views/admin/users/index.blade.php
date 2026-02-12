<x-layouts.admin title="Quản lý Tài khoản">

    @push('styles')
    <style>
        /* --- STYLE HIỆN ĐẠI (CLEAN UI) --- */
        :root { --primary-color: #4f46e5; --text-secondary: #64748b; }

        /* Card Custom */
        .card-custom {
            border: none; border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02);
        }

        /* Avatar User */
        .user-avatar {
            width: 40px; height: 40px; border-radius: 10px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: #475569; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1rem; margin-right: 12px;
        }

        /* Badge Roles */
        .badge-role { font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .role-admin   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; } /* Đỏ */
        .role-teacher { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; } /* Xanh đậm */
        .role-student { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; } /* Xám */

        /* Table Styles */
        .table-modern thead th {
            background-color: #f8fafc; color: var(--text-secondary); font-weight: 700; font-size: 0.75rem;
            text-transform: uppercase; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0;
        }
        .table-modern td { padding: 1rem 1.5rem; vertical-align: middle; color: #334155; }
    </style>
    @endpush

    <div class="container-fluid px-4 mt-4">
        
        {{-- THỐNG KÊ NHANH (Optional) --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-custom p-3 d-flex flex-row align-items-center bg-white">
                    <div class="p-3 bg-indigo-50 rounded-3 text-primary me-3"><i class="bi bi-people-fill fs-4"></i></div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ $users->total() }}</h5>
                        <small class="text-muted">Tổng tài khoản</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom bg-white mb-4">
            {{-- HEADER: TÌM KIẾM & NÚT THÊM --}}
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">Danh sách người dùng</h5>
                
                <div class="d-flex gap-2">
                    <form method="GET" class="d-flex">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Tìm tên/email..." value="{{ request('search') }}">
                        </div>
                    </form>
                    
                    {{-- Nút mở Modal Thêm mới --}}
                    <button class="btn btn-primary fw-bold shadow-sm d-flex align-items-center" onclick="openCreateModal()">
                        <i class="bi bi-plus-lg me-2"></i> Thêm mới
                    </button>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle table-modern mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Người dùng</th>
                            <th>Vai trò</th>
                            <th>Ngày tạo</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <div class="small text-muted">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->role == 'admin')
                                        <span class="badge badge-role role-admin"><i class="bi bi-shield-lock-fill me-1"></i> Admin</span>
                                    @elseif($user->role == 'teacher')
                                        <span class="badge badge-role role-teacher"><i class="bi bi-person-video3 me-1"></i> Giáo viên</span>
                                    @else
                                        <span class="badge badge-role role-student"><i class="bi bi-backpack2-fill me-1"></i> Học sinh</span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm border rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical text-secondary"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                            {{-- Nút Sửa (Gọi JS) --}}
                                            <li>
                                                <button class="dropdown-item py-2" 
                                                    onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')">
                                                    <i class="bi bi-pencil-square text-warning me-2"></i> Chỉnh sửa
                                                </button>
                                            </li>
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            
                                            {{-- Nút Xóa --}}
                                            <li>
                                                @if(Auth::id() != $user->id)
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Xóa tài khoản này sẽ mất toàn bộ dữ liệu liên quan. Bạn chắc chứ?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                                            <i class="bi bi-trash me-2"></i> Xóa tài khoản
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="dropdown-item text-muted disabled small">Không thể tự xóa</span>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($users->hasPages())
                <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL CHUNG (Dùng cho cả Thêm và Sửa) --}}
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Thêm tài khoản mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <form id="userForm" action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div id="methodField"></div> {{-- Nơi chứa @method('PUT') khi sửa --}}

                    <div class="modal-body p-4">
                        {{-- Tên --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="userName" class="form-control" required>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Email đăng nhập <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="userEmail" class="form-control" required>
                        </div>

                        {{-- Mật khẩu --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Mật khẩu</label>
                            <input type="password" name="password" class="form-control" placeholder="Để trống nếu không đổi">
                            <div class="form-text text-muted small" id="passHelp">Mặc định nên đặt: 123456</div>
                        </div>

                        {{-- Vai trò --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Vai trò hệ thống</label>
                            <select name="role" id="userRole" class="form-select bg-light" required>
                                <option value="student">Học sinh</option>
                                <option value="teacher">Giáo viên</option>
                                <option value="admin">Quản trị viên (Admin)</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 pt-0 pe-4 pb-4">
                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">Lưu thông tin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Modal instance
        var userModal = new bootstrap.Modal(document.getElementById('userModal'));

        function openCreateModal() {
            // Reset form về trạng thái Thêm mới
            document.getElementById('modalTitle').innerText = 'Thêm tài khoản mới';
            document.getElementById('userForm').action = "{{ route('admin.users.store') }}";
            document.getElementById('methodField').innerHTML = ''; // Xóa PUT
            
            document.getElementById('userName').value = '';
            document.getElementById('userEmail').value = '';
            document.getElementById('userEmail').readOnly = false; // Cho phép nhập email
            document.getElementById('userRole').value = 'student';
            document.getElementById('passHelp').innerText = 'Bắt buộc nhập khi tạo mới.';
            
            userModal.show();
        }

        function openEditModal(id, name, email, role) {
            // Chuyển form sang trạng thái Sửa
            document.getElementById('modalTitle').innerText = 'Cập nhật tài khoản';
            
            // Cập nhật URL action (Laravel Route)
            let url = "{{ route('admin.users.update', ':id') }}";
            url = url.replace(':id', id);
            document.getElementById('userForm').action = url;
            
            // Thêm method PUT
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            // Điền dữ liệu cũ
            document.getElementById('userName').value = name;
            document.getElementById('userEmail').value = email;
            document.getElementById('userEmail').readOnly = true; // Không cho sửa email (tránh lỗi duplicate)
            document.getElementById('userRole').value = role;
            document.getElementById('passHelp').innerText = 'Chỉ nhập nếu muốn đổi mật khẩu mới.';

            userModal.show();
        }
    </script>
    @endpush

</x-layouts.admin>