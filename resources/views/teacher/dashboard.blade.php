<x-layouts.teacher title="Tổng quan">
    
    {{-- 1. KHU VỰC THỐNG KÊ --}}
    <div class="row g-4 mb-4">
        {{-- Thẻ: Tổng số đề thi --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary bg-opacity-10">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-white p-3 rounded-circle text-primary shadow-sm">
                            <i class="bi bi-file-earmark-text-fill fs-4"></i>
                        </div>
                        <span class="badge bg-primary text-white">Đề thi</span>
                    </div>
                    {{-- SỬA: Dùng $totalExams thay vì $examCount --}}
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalExams }}</h2>
                    <p class="text-muted small mb-0">Đề thi đã tạo</p>
                </div>
            </div>
        </div>

        {{-- Thẻ: Ngân hàng câu hỏi --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-success bg-opacity-10">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-white p-3 rounded-circle text-success shadow-sm">
                            <i class="bi bi-collection-fill fs-4"></i>
                        </div>
                        <span class="badge bg-success text-white">Ngân hàng</span>
                    </div>
                    {{-- SỬA: Dùng $totalQuestions thay vì $questionCount --}}
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalQuestions }}</h2>
                    <p class="text-muted small mb-0">Câu hỏi trong hệ thống</p>
                </div>
            </div>
        </div>

        {{-- Thẻ: Học sinh --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-warning bg-opacity-10">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-white p-3 rounded-circle text-warning shadow-sm">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <span class="badge bg-warning text-dark">Học sinh</span>
                    </div>
                    {{-- SỬA: Biến mới $totalStudents --}}
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalStudents }}</h2>
                    <p class="text-muted small mb-0">Tài khoản học sinh</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. DANH SÁCH ĐỀ THI GẦN ĐÂY --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history me-2"></i> Đề thi vừa tạo</h6>
            <a href="{{ route('teacher.exams.index') }}" class="btn btn-sm btn-light text-primary fw-bold">Xem tất cả</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4">Tên đề thi</th>
                        <th>Trạng thái</th>
                        <th>Số câu</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- SỬA: Dùng $recentExams thay vì $myExams --}}
                    @forelse($recentExams as $exam)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $exam->title }}</td>
                            <td>
                                @if($exam->is_public)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 rounded-pill">
                                        <i class="bi bi-globe"></i> Public
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1 rounded-pill">
                                        <i class="bi bi-lock-fill"></i> Private
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $exam->total_questions }} câu</span>
                            </td>
                            <td class="text-muted small">{{ $exam->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Tùy chọn
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('teacher.exams.edit', $exam->id) }}">
                                                <i class="bi bi-pencil-square text-primary me-2"></i> Chỉnh sửa
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('teacher.exams.results', $exam->id) }}">
                                                <i class="bi bi-bar-chart-line text-info me-2"></i> Xem kết quả
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Chưa có đề thi nào. <a href="{{ route('teacher.exams.create') }}">Tạo ngay</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.teacher>