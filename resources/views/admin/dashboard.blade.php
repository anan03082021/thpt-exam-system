<x-layouts.admin title="Tổng quan hệ thống">

    @push('styles')
    <style>
        /* --- STYLE DASHBOARD ADMIN --- */
        .stat-card {
            border: none; border-radius: 16px; padding: 1.5rem;
            background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s; height: 100%; position: relative; overflow: hidden;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        
        .icon-box {
            width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem;
        }
        
        /* Màu sắc các thẻ */
        .bg-indigo-soft { background: #eef2ff; color: #4f46e5; }
        .bg-green-soft { background: #ecfdf5; color: #059669; }
        .bg-amber-soft { background: #fffbeb; color: #d97706; }
        .bg-blue-soft { background: #eff6ff; color: #2563eb; }
        .bg-teal-soft { background: #f0fdfa; color: #0d9488; }
        .bg-rose-soft { background: #fff1f2; color: #e11d48; }

        /* Action Buttons */
        .btn-quick {
            text-align: left; padding: 1.25rem; border: 1px solid #e2e8f0; border-radius: 12px;
            background: white; transition: all 0.2s; color: #334155; font-weight: 600;
            display: flex; align-items: center; justify-content: space-between;
        }
        .btn-quick:hover {
            border-color: #4f46e5; color: #4f46e5; background: #f8fafc; text-decoration: none;
        }
        .btn-quick i { font-size: 1.5rem; opacity: 0.8; }
    </style>
    @endpush

    <div class="container-fluid px-4 mt-4">
        
        {{-- WELCOME BANNER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Dashboard Quản Trị</h2>
                <p class="text-muted mb-0">Xin chào, {{ Auth::user()->name }}! Dưới đây là tổng quan hệ thống.</p>
            </div>
            <div class="d-none d-md-block">
                <span class="badge bg-dark px-3 py-2 rounded-pill fw-normal">Phiên bản 1.0</span>
            </div>
        </div>

        {{-- 1. THỐNG KÊ NGƯỜI DÙNG --}}
        <h6 class="text-uppercase text-muted fw-bold small mb-3 ls-1">Tài khoản & Người dùng</h6>
        <div class="row g-4 mb-4">
            {{-- Tổng User --}}
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="icon-box bg-indigo-soft"><i class="bi bi-people-fill"></i></div>
                            <h3 class="fw-bold mb-0">{{ $stats['users'] ?? 0 }}</h3>
                            <span class="text-muted small">Tổng tài khoản</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Giáo viên --}}
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-green-soft"><i class="bi bi-person-video3"></i></div>
                    <h3 class="fw-bold mb-0">{{ $stats['teachers'] ?? 0 }}</h3>
                    <span class="text-muted small">Giáo viên</span>
                </div>
            </div>

            {{-- Học sinh --}}
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-amber-soft"><i class="bi bi-backpack2-fill"></i></div>
                    <h3 class="fw-bold mb-0">{{ $stats['students'] ?? 0 }}</h3>
                    <span class="text-muted small">Học sinh</span>
                </div>
            </div>
        </div>

        {{-- 2. THỐNG KÊ HỆ THỐNG --}}
        <h6 class="text-uppercase text-muted fw-bold small mb-3 ls-1 mt-4">Hoạt động hệ thống</h6>
        <div class="row g-4 mb-5">
            {{-- Đề thi --}}
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-blue-soft"><i class="bi bi-file-earmark-text-fill"></i></div>
                    <h3 class="fw-bold mb-0">{{ $stats['exams'] ?? 0 }}</h3>
                    <span class="text-muted small">Đề thi trong ngân hàng</span>
                </div>
            </div>

            {{-- Ca thi --}}
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-teal-soft"><i class="bi bi-broadcast"></i></div>
                    <h3 class="fw-bold mb-0">{{ $stats['sessions'] ?? 0 }}</h3>
                    <span class="text-muted small">Ca thi đã tổ chức</span>
                </div>
            </div>

            {{-- Tin nhắn (MỚI) --}}
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-rose-soft"><i class="bi bi-chat-dots-fill"></i></div>
                    <h3 class="fw-bold mb-0">{{ $stats['messages'] ?? 0 }}</h3>
                    <span class="text-muted small">Tin nhắn diễn đàn</span>
                </div>
            </div>
        </div>

        {{-- 3. CHỨC NĂNG NHANH --}}
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-shield-lock-fill me-2"></i> Quản trị viên</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-grid gap-3">
                            <a href="{{ route('admin.users.index') }}" class="btn-quick">
                                <span><i class="bi bi-people-fill me-2 text-primary"></i> Quản lý Tài khoản</span>
                                <i class="bi bi-chevron-right small text-muted"></i>
                            </a>
                            <a href="{{ route('admin.forum.index') }}" class="btn-quick">
                                <span><i class="bi bi-chat-quote-fill me-2 text-danger"></i> Kiểm duyệt Diễn đàn</span>
                                <i class="bi bi-chevron-right small text-muted"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="fw-bold mb-0 text-success"><i class="bi bi-book-half me-2"></i> Công tác Chuyên môn</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="{{ route('teacher.questions.index') }}" class="btn-quick h-100 flex-column align-items-start p-3">
                                    <i class="bi bi-database-fill text-warning mb-2"></i>
                                    <span>Ngân hàng câu hỏi</span>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('teacher.exams.index') }}" class="btn-quick h-100 flex-column align-items-start p-3">
                                    <i class="bi bi-file-earmark-text-fill text-info mb-2"></i>
                                    <span>Quản lý Đề thi</span>
                                </a>
                            </div>
                            <div class="col-md-12">
                                <a href="{{ route('teacher.sessions.index') }}" class="btn-quick">
                                    <span><i class="bi bi-broadcast me-2 text-success"></i> Tổ chức Ca thi</span>
                                    <i class="bi bi-chevron-right small text-muted"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.admin>