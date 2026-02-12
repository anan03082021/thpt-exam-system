<x-layouts.shared title="Quản lý đề thi">

    @push('styles')
    <style>
        /* --- STYLE HIỆN ĐẠI (CLEAN UI) --- */
        :root { --primary-color: #4f46e5; --primary-bg: #eef2ff; --text-secondary: #64748b; }

        /* Card Styles */
        .card-custom {
            border: none;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.03), 0 4px 12px rgba(0,0,0,0.05);
            border-radius: 16px;
        }

        /* Table Styles */
        .table-modern { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-modern thead th {
            background-color: #f8fafc; color: var(--text-secondary);
            font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;
            padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0;
        }
        .table-modern tbody tr { transition: background-color 0.2s; }
        .table-modern tbody tr:hover { background-color: #fcfcfc; }
        .table-modern td {
            padding: 1.2rem 1.5rem; border-bottom: 1px solid #f1f5f9;
            vertical-align: middle; color: #334155; font-size: 0.95rem;
        }
        .table-modern tbody tr:last-child td { border-bottom: none; }

        /* Exam Icon & Title */
        .exam-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            background: var(--primary-bg); color: var(--primary-color); font-size: 1.4rem;
        }
        .exam-title {
            font-weight: 700; color: #1e293b; text-decoration: none; font-size: 1rem;
            display: block; margin-bottom: 2px; transition: color 0.2s;
        }
        .exam-title:hover { color: var(--primary-color); }
        .exam-desc { color: #64748b; font-size: 0.85rem; max-width: 400px; }
        
        .meta-text { font-size: 0.8rem; color: #94a3b8; display: flex; align-items: center; gap: 15px; margin-top: 6px; }

        /* Badges */
        .badge-soft { padding: 0.5em 1em; font-size: 0.75rem; font-weight: 600; border-radius: 8px; }
        .badge-soft-success { background-color: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .badge-soft-secondary { background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
        
        /* Buttons */
        .btn-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border: none; color: white; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); transition: all 0.2s;
        }
        .btn-gradient:hover { transform: translateY(-2px); color: white; box-shadow: 0 6px 12px -1px rgba(79, 70, 229, 0.3); }
        
        .btn-icon {
            width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid #e2e8f0; color: #64748b; background: white; transition: all 0.2s;
        }
        .btn-icon:hover { border-color: var(--primary-color); color: var(--primary-color); background: #f8fafc; }
    </style>
    @endpush

    <div class="card card-custom bg-white mt-3" style="overflow: visible;">
        
        {{-- Header & Toolbar --}}
        <div class="p-4 border-bottom">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center">
                    <div class="bg-indigo-50 text-primary rounded-3 p-3 me-3 d-none d-md-block">
                        <i class="bi bi-folder2-open fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">Ngân hàng đề thi</h4>
                        <div class="text-muted small">
                            <span class="fw-bold text-dark">{{ $exams->total() }}</span> đề thi đang quản lý
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    {{-- Form Tìm kiếm (Cần update Controller để hoạt động) --}}
                    <form action="{{ route('teacher.exams.index') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Tìm nhanh..." value="{{ request('search') }}" style="min-width: 200px;">
                        </div>
                    </form>

                    <a href="{{ route('teacher.exams.create') }}" class="btn btn-gradient fw-bold px-4 rounded-3 d-flex align-items-center">
                        <i class="bi bi-plus-lg me-2"></i> <span>Tạo mới</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Danh sách --}}
        <div>
            <table class="table-modern">
                <thead>
                    <tr>
                        <th width="45%">Thông tin đề thi</th>
                        <th width="15%" class="text-center">Số câu hỏi</th>
                        <th width="15%" class="text-center">Thời lượng</th>
                        <th width="15%" class="text-center">Trạng thái</th>
                        <th width="10%" class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            {{-- Cột 1: Thông tin chính --}}
                            <td>
                                <div class="d-flex align-items-start">
                                    <div class="exam-icon me-3 shadow-sm">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('teacher.exams.edit', $exam->id) }}" class="exam-title text-truncate" style="max-width: 450px;" title="{{ $exam->title }}">
                                            {{ $exam->title }}
                                        </a>
                                        {{-- [MỚI] Hiển thị mô tả ngắn --}}
                                        <div class="exam-desc text-truncate small mb-1">
                                            {{ $exam->description ?? 'Chưa có mô tả' }}
                                        </div>
                                        <div class="meta-text">
                                            <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.7rem;">#{{ $exam->id }}</span>
                                            <span><i class="bi bi-calendar3 me-1"></i> {{ $exam->created_at->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Cột 2: Số câu --}}
                            <td class="text-center">
                                <span class="fw-bold text-dark fs-6">{{ $exam->total_questions ?? 0 }}</span>
                                <span class="text-muted small d-block">câu</span>
                            </td>

                            {{-- Cột 3: Thời gian --}}
                            <td class="text-center">
                                <span class="badge bg-light text-dark border fw-normal px-3 py-2 rounded-pill">
                                    <i class="bi bi-clock me-1 text-primary"></i> {{ $exam->duration }}'
                                </span>
                            </td>

                            {{-- Cột 4: Trạng thái --}}
                            <td class="text-center">
                                @if($exam->is_public)
                                    <span class="badge badge-soft badge-soft-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> Công khai
                                    </span>
                                @else
                                    <span class="badge badge-soft badge-soft-secondary">
                                        <i class="bi bi-eye-slash-fill me-1"></i> Bản nháp
                                    </span>
                                @endif
                            </td>

                            {{-- Cột 5: Menu hành động --}}
                            <td class="text-end pe-4" style="position: relative;">
                                <div class="btn-group dropstart">
                                    <button class="btn btn-icon shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 p-1" style="min-width: 200px; z-index: 9999;">
                                        <li>
                                            <a class="dropdown-item py-2 rounded-2 d-flex align-items-center" href="{{ route('teacher.exams.edit', $exam->id) }}">
                                                <span class="bg-primary bg-opacity-10 text-primary rounded p-1 me-2"><i class="bi bi-pencil-square"></i></span>
                                                Chỉnh sửa đề
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 rounded-2 d-flex align-items-center" href="{{ route('teacher.exams.results', $exam->id) }}">
                                                <span class="bg-info bg-opacity-10 text-info rounded p-1 me-2"><i class="bi bi-bar-chart-line"></i></span>
                                                Xem kết quả
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
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-5">
                                    <div class="mb-3">
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="bi bi-inbox text-muted opacity-50" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark">Chưa có đề thi nào</h6>
                                    <p class="text-muted small mb-4">Bạn chưa tạo đề thi nào hoặc không tìm thấy kết quả.</p>
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

        {{-- Phân trang --}}
        @if($exams->hasPages())
            <div class="card-footer bg-white border-top py-4 d-flex justify-content-center">
                {{ $exams->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</x-layouts.shared>