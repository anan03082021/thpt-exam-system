<x-layouts.teacher title="Kết quả: {{ $exam->title }}">

    @push('styles')
    <style>
        /* --- STYLE HIỆN ĐẠI (CLEAN UI) --- */
        :root {
            --primary-color: #4f46e5;
            --primary-bg: #eef2ff;
            --text-secondary: #64748b;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border: none;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        /* Màu sắc thống kê */
        .stat-primary .stat-icon { background: #eef2ff; color: #4f46e5; }
        .stat-success .stat-icon { background: #ecfdf5; color: #059669; }
        .stat-warning .stat-icon { background: #fffbeb; color: #d97706; }
        .stat-danger .stat-icon  { background: #fef2f2; color: #dc2626; }

        /* Card Custom */
        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.03), 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        /* Table Modern */
        .table-modern { width: 100%; border-collapse: separate; border-spacing: 0; }
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
        .table-modern tbody tr:hover { background-color: #fcfcfc; }
        .table-modern td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
            font-size: 0.95rem;
        }
        .table-modern tbody tr:last-child td { border-bottom: none; }

        /* Avatar User */
        .user-avatar {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: #475569;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1rem;
            border: 1px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Badges */
        .badge-score { font-size: 0.9rem; font-weight: 700; padding: 0.3em 0.8em; border-radius: 8px; }
        .badge-score-high { background-color: #ecfdf5; color: #059669; }
        .badge-score-mid { background-color: #eff6ff; color: #2563eb; }
        .badge-score-low { background-color: #fef2f2; color: #dc2626; }

        /* Buttons */
        .btn-light-custom { background: white; border: 1px solid #e2e8f0; color: #475569; font-weight: 600; }
        .btn-light-custom:hover { background: #f8fafc; border-color: #cbd5e1; color: #1e293b; }
    </style>
    @endpush

    {{-- HEADER TRANG (ĐÃ XÓA PHẦN QUAY LẠI) --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            {{-- Đã xóa div chứa link Quay lại ở đây --}}
            <h4 class="fw-bold text-dark mb-0" style="font-size: 1.75rem;">{{ $exam->title }}</h4>
            <p class="text-muted small mb-0 mt-2">
                <i class="bi bi-clock me-1"></i> Ngày tạo: {{ $exam->created_at->format('d/m/Y') }} &bull; 
                <i class="bi bi-people me-1 ms-2"></i> {{ $attempts->count() }} bài nộp
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-light-custom px-3 py-2 rounded-3 shadow-sm">
                <i class="bi bi-file-earmark-excel text-success me-2"></i> Xuất Excel
            </button>
        </div>
    </div>

    {{-- 1. DASHBOARD THỐNG KÊ --}}
    <div class="row g-4 mb-4">
        {{-- Card 1: Tổng bài --}}
        <div class="col-md-3">
            <div class="stat-card stat-primary">
                <div class="stat-icon"><i class="bi bi-files"></i></div>
                <h2 class="fw-bold text-dark mb-0">{{ $attempts->count() }}</h2>
                <span class="text-secondary small fw-bold text-uppercase ls-1">Tổng số bài nộp</span>
            </div>
        </div>
        {{-- Card 2: Điểm trung bình --}}
        <div class="col-md-3">
            <div class="stat-card stat-warning">
                <div class="stat-icon"><i class="bi bi-pie-chart"></i></div>
                <h2 class="fw-bold text-dark mb-0">{{ number_format($attempts->avg('total_score'), 2) }}</h2>
                <span class="text-secondary small fw-bold text-uppercase ls-1">Điểm trung bình</span>
            </div>
        </div>
        {{-- Card 3: Cao nhất --}}
        <div class="col-md-3">
            <div class="stat-card stat-success">
                <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <h2 class="fw-bold text-dark mb-0">{{ $attempts->max('total_score') ?? 0 }}</h2>
                <span class="text-secondary small fw-bold text-uppercase ls-1">Điểm cao nhất</span>
            </div>
        </div>
        {{-- Card 4: Thấp nhất --}}
        <div class="col-md-3">
            <div class="stat-card stat-danger">
                <div class="stat-icon"><i class="bi bi-graph-down-arrow"></i></div>
                <h2 class="fw-bold text-dark mb-0">{{ $attempts->min('total_score') ?? 0 }}</h2>
                <span class="text-secondary small fw-bold text-uppercase ls-1">Điểm thấp nhất</span>
            </div>
        </div>
    </div>

    {{-- 2. DANH SÁCH CHI TIẾT --}}
    <div class="card card-custom bg-white">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-dark mb-0">Bảng điểm chi tiết</h5>
            
            {{-- Search Box giả lập --}}
            <div class="input-group" style="width: 280px;">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0 shadow-none" placeholder="Tìm kiếm học sinh...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th width="35%">Học sinh</th>
                        <th width="20%">Thời gian nộp</th>
                        <th width="15%" class="text-center">Điểm số</th>
                        <th width="15%" class="text-center">Xếp loại</th>
                        <th width="10%" class="text-end">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempts as $index => $attempt)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3">
                                        {{ substr($attempt->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $attempt->user->name ?? 'Unknown' }}</div>
                                        <div class="small text-muted">{{ $attempt->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-secondary">
                                <i class="bi bi-calendar3 me-1"></i> {{ $attempt->created_at->format('H:i d/m/Y') }}
                            </td>
                            <td class="text-center">
                                @php $s = $attempt->total_score; @endphp
                                <span class="badge-score {{ $s >= 8 ? 'badge-score-high' : ($s >= 5 ? 'badge-score-mid' : 'badge-score-low') }}">
                                    {{ $s }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($s >= 8) <span class="text-success fw-bold small">Giỏi</span>
                                @elseif($s >= 6.5) <span class="text-primary fw-bold small">Khá</span>
                                @elseif($s >= 5) <span class="text-warning fw-bold small">Trung bình</span>
                                @else <span class="text-danger fw-bold small">Yếu</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border rounded-circle shadow-sm text-primary" title="Xem bài làm">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                        <i class="bi bi-inbox text-muted fs-2"></i>
                                    </div>
                                    <h6 class="fw-bold text-secondary">Chưa có bài nộp nào</h6>
                                    <p class="text-muted small">Danh sách sẽ hiển thị khi học sinh hoàn thành bài thi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.teacher>