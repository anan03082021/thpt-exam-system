<x-layouts.admin title="Tạo tài khoản mới">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2"></i> Tạo tài khoản mới</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        
                        {{-- Vai trò --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Loại tài khoản <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="role_student" value="student" checked>
                                    <label class="form-check-label" for="role_student">Học sinh</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="role_teacher" value="teacher">
                                    <label class="form-check-label" for="role_teacher">Giáo viên</label>
                                </div>
                            </div>
                        </div>

                        {{-- Tên --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="VD: Nguyễn Văn A" required>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email (Tên đăng nhập) <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="VD: hs001@thpt.edu.vn" required>
                            <div class="form-text text-muted">Email này sẽ được dùng để đăng nhập hệ thống.</div>
                        </div>

                        {{-- Mật khẩu --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Mật khẩu</label>
                            <input type="password" name="password" class="form-control" placeholder="Để trống sẽ mặc định là: 123456">
                            <div class="form-text text-success fw-bold"><i class="bi bi-info-circle"></i> Mặc định: 123456</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                            <button type="submit" class="btn btn-primary fw-bold">Lưu tài khoản</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>