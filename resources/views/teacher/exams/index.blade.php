<x-layouts.teacher title="Quản lý đề thi">

    @push('styles')
    <style>
        /* Gradient Header */
        .card-header-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
        }
        
        /* Badges */
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 30px;
        }
        .bg-published { background-color: #d1fae5; color: #059669; } /* Xanh lá */
        .bg-draft { background-color: #f3f4f6; color: #6b7280; }     /* Xám */

        /* Table Styles */
        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }
        .exam-title {
            font-weight: 600;
            color: #1e293b;
            text-decoration: none;
            transition: color 0.2s;
        }
        .exam-title:hover {
            color: #4f46e5;
        }
    </style>
    @endpush

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Quản lý đề thi</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        
        {{-- Header & Toolbar --}}
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                    <i class="bi bi-folder2-open fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Danh sách đề thi</h5>
                    <p class="mb-0 small text-muted">Quản lý tất cả đề thi bạn đã tạo</p>
                </div>
            </div>
            
            <a href="{{ route('teacher.exams.create') }}" class="btn btn-indigo shadow-sm fw-bold px-3" style="background: #4f46e5; color: white;">
                <i class="bi bi-plus-lg me-1"></i> Tạo đề thi mới
            </a>
        </div>

        {{-- Table Content --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3" width="5%">ID</th>
                            <th width="35%">Tên đề thi</th>
                            <th class="text-center" width="15%">Cấu trúc</th>
                            <th class="text-center" width="15%">Thời lượng</th>
                            <th class="text-center" width="15%">Trạng thái</th>
                            <th class="text-end pe-4" width="15%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#{{ $exam->id }}</td>
                                
                                {{-- Tên đề & Ngày tạo --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 d-none d-md-block">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px;">
                                                <i class="bi bi-file-text text-secondary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="#" class="exam-title d-block text-truncate" style="max-width: 250px;" title="{{ $exam->title }}">
                                                {{ $exam->title }}
                                            </a>
                                            <div class="small text-muted">
                                                <i class="bi bi-clock-history me-1"></i> {{ $exam->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Số câu hỏi --}}
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">
                                        {{ $exam->total_questions ?? 0 }} câu hỏi
                                    </span>
                                    <div class="small text-muted mt-1">{{ $exam->topic->name ?? 'Tổng hợp' }}</div>
                                </td>

                                {{-- Thời lượng --}}
                                <td class="text-center">
                                    <span class="fw-bold text-dark">{{ $exam->duration }}</span> <small class="text-muted">phút</small>
                                </td>

                                {{-- Trạng thái --}}
                                <td class="text-center">
                                    @if($exam->status == 'published' || $exam->is_public) 
                                        {{-- Giả sử bạn dùng cột status hoặc is_public --}}
                                        <span class="status-badge bg-published">
                                            <i class="bi bi-check-circle-fill me-1"></i> Công khai
                                        </span>
                                    @else
                                        <span class="status-badge bg-draft">
                                            <i class="bi bi-pencil-fill me-1"></i> Nháp
                                        </span>
                                    @endif
                                </td>

                                {{-- Hành động --}}
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Tùy chọn
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
    {{-- 1. Xem kết quả --}}
    <li>
        <a class="dropdown-item" href="{{ route('teacher.exams.results', $exam->id) }}">
            <i class="bi bi-bar-chart-line text-info me-2"></i> Xem kết quả
        </a>
    </li>

    {{-- 2. Chỉnh sửa (ĐÃ CẬP NHẬT) --}}
    <li>
        {{-- Thay href="#" bằng route trỏ đến hàm edit --}}
        <a class="dropdown-item" href="{{ route('teacher.exams.edit', $exam->id) }}">
            <i class="bi bi-pencil-square text-primary me-2"></i> Chỉnh sửa
        </a>
    </li>

    <li><hr class="dropdown-divider"></li>

    {{-- 3. Xóa đề thi (ĐÃ CẬP NHẬT) --}}
    <li>
        {{-- Thay action="#" bằng route destroy --}}
        <form action="{{ route('teacher.exams.destroy', $exam->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đề thi này không? Hành động này không thể hoàn tác.');">
            @csrf
            @method('DELETE') {{-- Bắt buộc phải có dòng này để Laravel hiểu là lệnh Xóa --}}
            
            <button type="submit" class="dropdown-item text-danger">
                <i class="bi bi-trash me-2"></i> Xóa đề thi
            </button>
        </form>
    </li>
</ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="mb-3 opacity-25">
                                        <i class="bi bi-folder-x" style="font-size: 3rem;"></i>
                                    </div>
                                    <h6 class="fw-bold text-secondary">Chưa có đề thi nào</h6>
                                    <p class="text-muted small">Hãy tạo đề thi đầu tiên để bắt đầu tổ chức thi.</p>
                                    <a href="{{ route('teacher.exams.create') }}" class="btn btn-sm btn-outline-primary">
                                        Tạo ngay
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($exams->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $exams->links() }}
                </div>
            @endif
        </div>
    </div>

</x-layouts.teacher>