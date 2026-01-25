<x-layouts.teacher title="Quản lý đề thi">

    @push('styles')
    <style>
        /* --- STYLE HIỆN ĐẠI (CLEAN UI) --- */
        :root {
            --primary-color: #4f46e5;
            --primary-bg: #eef2ff;
            --text-secondary: #64748b;
        }

        /* Card Styles */
        .card-custom {
            border: none;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.03), 0 4px 12px rgba(0,0,0,0.05);
            border-radius: 16px;
            /* QUAN TRỌNG: Không set overflow:hidden để menu hiện ra ngoài */
        }

        /* Table Styles */
        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-modern thead th {
            background-color: #f8fafc;
            color: var(--text-secondary);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .table-modern tbody tr { transition: background-color 0.2s; }
        .table-modern tbody tr:hover { background-color: #fcfcfc; }
        .table-modern td {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
            font-size: 0.95rem;
        }
        .table-modern tbody tr:last-child td { border-bottom: none; }

        /* Icon & Text Styling */
        .exam-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            background: var(--primary-bg); color: var(--primary-color);
            font-size: 1.4rem;
        }
        .exam-title {
            font-weight: 700; color: #1e293b; text-decoration: none;
            font-size: 1rem; display: block;
            margin-bottom: 4px; transition: color 0.2s;
        }
        .exam-title:hover { color: var(--primary-color); }
        .meta-text { font-size: 0.85rem; color: #94a3b8; display: flex; align-items: center; gap: 15px; }

        /* Status Badges */
        .badge-soft { padding: 0.5em 1em; font-size: 0.75rem; font-weight: 600; border-radius: 8px; }
        .badge-soft-success { background-color: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .badge-soft-secondary { background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
        
        /* Buttons */
        .btn-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border: none; color: white;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
            transition: all 0.2s;
        }
        .btn-gradient:hover { transform: translateY(-2px); color: white; box-shadow: 0 6px 12px -1px rgba(79, 70, 229, 0.3); }
        
        .btn-icon {
            width: 36px; height: 36px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid #e2e8f0; color: #64748b; background: white;
            transition: all 0.2s;
        }
        .btn-icon:hover { border-color: var(--primary-color); color: var(--primary-color); background: #f8fafc; }
    </style>
    @endpush

    {{-- Thêm style="overflow: visible" cho card để menu xổ ra ngoài được --}}
    <div class="card card-custom bg-white mt-3" style="overflow: visible;">
        
        {{-- Header --}}
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-indigo-50 text-primary rounded-3 p-3 me-3 d-none d-md-block">
                    <i class="bi bi-folder2-open fs-4"></i>
                </div>
                <div>
                    <h4 class="mb-1 fw-bold text-dark">Danh sách đề thi</h4>
                    <p class="mb-0 text-muted small">Quản lý toàn bộ ngân hàng đề thi của bạn</p>
                </div>
            </div>
            
            <a href="{{ route('teacher.exams.create') }}" class="btn btn-gradient fw-bold px-4 py-2 rounded-3 d-flex align-items-center">
                <i class="bi bi-plus-lg me-2"></i> 
                <span>Tạo đề mới</span>
            </a>
        </div>

        {{-- Toolbar --}}
        <div class="px-4 py-2 bg-light bg-opacity-25 border-bottom d-flex justify-content-between align-items-center">
            <div class="text-secondary fw-bold small text-uppercase ls-1">
                <i class="bi bi-layers me-2"></i> Tổng số: {{ $exams->total() }} đề thi
            </div>
        </div>

        {{-- 
            [ĐÃ SỬA] 
            1. Dùng div thường (không dùng table-responsive).
            2. Xóa min-height để bảng co gọn lại, không tạo khoảng trắng thừa.
        --}}
        <div>
            <table class="table-modern">
                <thead>
                    <tr>
                        <th width="40%">Thông tin đề thi</th>
                        <th width="15%" class="text-center">Cấu trúc</th>
                        <th width="15%" class="text-center">Thời lượng</th>
                        <th width="15%" class="text-center">Trạng thái</th>
                        <th width="15%" class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            {{-- Cột 1 --}}
                            <td>
                                <div class="d-flex align-items-start">
                                    <div class="exam-icon me-3 shadow-sm">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('teacher.exams.results', $exam->id) }}" class="exam-title text-truncate" style="max-width: 350px;" title="{{ $exam->title }}">
                                            {{ $exam->title }}
                                        </a>
                                        <div class="meta-text mt-2">
                                            <span><i class="bi bi-hash"></i> {{ $exam->id }}</span>
                                            <span><i class="bi bi-calendar3"></i> {{ $exam->created_at->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Cột 2 --}}
                            <td class="text-center">
                                <span class="fw-bold text-dark fs-6">{{ $exam->total_questions ?? 0 }}</span>
                                <span class="text-muted small d-block">câu hỏi</span>
                            </td>

                            {{-- Cột 3 --}}
                            <td class="text-center">
                                <span class="badge bg-light text-dark border fw-normal px-3 py-2 rounded-pill">
                                    <i class="bi bi-clock me-1 text-primary"></i> {{ $exam->duration }}'
                                </span>
                            </td>

                            {{-- Cột 4 --}}
                            <td class="text-center">
                                @if($exam->status == 'published' || $exam->is_public)
                                    <span class="badge badge-soft badge-soft-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> Công khai
                                    </span>
                                @else
                                    <span class="badge badge-soft badge-soft-secondary">
                                        <i class="bi bi-file-earmark me-1"></i> Bản nháp
                                    </span>
                                @endif
                            </td>

                            {{-- Cột 5: Hành động (Menu dropstart) --}}
                            <td class="text-end pe-4" style="position: relative;">
                                <div class="btn-group dropstart">
                                    {{-- Nút bấm --}}
                                    <button class="btn btn-icon shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    
                                    {{-- Menu --}}
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 p-1" style="min-width: 200px; z-index: 9999;">
                                        <li>
                                            <a class="dropdown-item py-2 rounded-2 d-flex align-items-center" href="{{ route('teacher.exams.results', $exam->id) }}">
                                                <span class="bg-info bg-opacity-10 text-info rounded p-1 me-2"><i class="bi bi-bar-chart-line"></i></span>
                                                Xem kết quả
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 rounded-2 d-flex align-items-center" href="{{ route('teacher.exams.edit', $exam->id) }}">
                                                <span class="bg-warning bg-opacity-10 text-warning rounded p-1 me-2"><i class="bi bi-pencil-square"></i></span>
                                                Chỉnh sửa
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form action="{{ route('teacher.exams.destroy', $exam->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đề thi này không?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item py-2 rounded-2 text-danger d-flex align-items-center">
                                                    <span class="bg-danger bg-opacity-10 text-danger rounded p-1 me-2"><i class="bi bi-trash"></i></span>
                                                    Xóa đề thi
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Empty State --}}
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-5">
                                    <div class="mb-3">
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="bi bi-inbox text-muted opacity-50" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark">Chưa có đề thi nào</h6>
                                    <p class="text-muted small mb-4">Hãy bắt đầu tạo ngân hàng đề thi đầu tiên của bạn.</p>
                                    <a href="{{ route('teacher.exams.create') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-bold">
                                        <i class="bi bi-plus-lg me-1"></i> Tạo đề ngay
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination (Chỉ hiện khi có nhiều trang) --}}
        @if($exams->hasPages())
            <div class="card-footer bg-white border-top py-4 d-flex justify-content-center">
                {{ $exams->links('pagination::bootstrap-5') }}
            </div>
        @endif
        
        {{-- [ĐÃ XÓA] Khoảng trắng thừa 100px đã bị loại bỏ --}}
    </div>

</x-layouts.teacher>