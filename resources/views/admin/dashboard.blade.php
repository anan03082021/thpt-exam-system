<x-layouts.admin title="Tổng quan hệ thống">

    <div class="row g-4 mb-4">
        {{-- Thống kê Học sinh --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-4 text-primary">
                            <i class="bi bi-mortarboard-fill fs-4"></i>
                        </div>
                        <span class="badge bg-light text-secondary border">Học sinh</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ \App\Models\User::where('role', 'student')->count() }}</h3>
                    <p class="text-muted small mb-0">Tài khoản học sinh</p>
                </div>
            </div>
        </div>

        {{-- Thống kê Giáo viên --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-4 text-success">
                            <i class="bi bi-person-video3 fs-4"></i>
                        </div>
                        <span class="badge bg-light text-secondary border">Giáo viên</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ \App\Models\User::where('role', 'teacher')->count() }}</h3>
                    <p class="text-muted small mb-0">Tài khoản giáo viên</p>
                </div>
            </div>
        </div>

        {{-- Thống kê Khác (Ví dụ số đề thi) --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-4 text-warning">
                            <i class="bi bi-file-earmark-text-fill fs-4"></i>
                        </div>
                        <span class="badge bg-light text-secondary border">Đề thi</span>
                    </div>
                    {{-- Giả sử có Model Exam --}}
                    <h3 class="fw-bold mb-1">{{ \App\Models\Exam::count() }}</h3>
                    <p class="text-muted small mb-0">Tổng số đề thi</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Khu vực chức năng nhanh --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h6 class="fw-bold mb-0">🚀 Chức năng quản trị</h6>
        </div>
        <div class="card-body p-4">
            <p class="text-muted mb-4">Truy cập nhanh các chức năng quản lý hệ thống.</p>
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary w-100 py-3 fw-bold border-2">
                        <i class="bi bi-people-fill me-2"></i> Quản lý Tài khoản
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-outline-success w-100 py-3 fw-bold border-2">
                        <i class="bi bi-person-plus-fill me-2"></i> Tạo User Mới
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-layouts.admin>