<x-layouts.teacher title="Kết quả thi: {{ $exam->title }}">

    @push('styles')
    <style>
        /* Header Gradient */
        .card-header-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            border-left: 4px solid transparent;
            height: 100%;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        
        .border-l-primary { border-left-color: #4f46e5; }
        .border-l-success { border-left-color: #10b981; }
        .border-l-warning { border-left-color: #f59e0b; }
        .border-l-danger { border-left-color: #ef4444; }

        .icon-circle {
            width: 48px; height: 48px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Table & Badges */
        .table-hover tbody tr:hover { background-color: #f8fafc; }
        .score-badge { font-size: 1.1rem; font-weight: 700; }
        
        .rank-badge {
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 30px;
            font-weight: 600;
            text-transform: uppercase;
        }
    </style>
    @endpush

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}" class="text-decoration-none text-muted">Đề thi</a></li>
            <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Kết quả chi tiết</li>
        </ol>
    </nav>

    {{-- Header Title --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">📊 Kết quả thi: {{ $exam->title }}</h4>
            <p class="text-muted small mb-0">Xem thống kê và danh sách bài làm của học sinh</p>
        </div>
        <div>
            {{-- Nút Export giả lập (Có thể phát triển sau) --}}
            <button class="btn btn-light border shadow-sm me-2 text-success fw-bold">
                <i class="bi bi-file-earmark-excel-fill"></i> Xuất Excel
            </button>
            <a href="{{ route('teacher.exams.index') }}" class="btn btn-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    {{-- 1. THỐNG KÊ TỔNG QUAN --}}
    <div class="row g-4 mb-5">
        {{-- Tổng số bài --}}
        <div class="col-md-3">
            <div class="stat-card border-l-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted text-uppercase small fw-bold mb-1">Số lượng bài thi</p>
                        <h2 class="fw-bold text-dark mb-0">{{ $attempts->count() }}</h2>
                    </div>
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Điểm trung bình --}}
        <div class="col-md-3">
            <div class="stat-card border-l-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted text-uppercase small fw-bold mb-1">Điểm trung bình</p>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($attempts->avg('total_score'), 2) }}</h2>
                    </div>
                    <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Điểm cao nhất --}}
        <div class="col-md-3">
            <div class="stat-card border-l-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted text-uppercase small fw-bold mb-1">Cao nhất</p>
                        <h2 class="fw-bold text-success mb-0">{{ $attempts->max('total_score') ?? 0 }}</h2>
                    </div>
                    <div class="icon-circle bg-success bg-opacity-10 text-success">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Điểm thấp nhất --}}
        <div class="col-md-3">
            <div class="stat-card border-l-danger">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted text-uppercase small fw-bold mb-1">Thấp nhất</p>
                        <h2 class="fw-bold text-danger mb-0">{{ $attempts->min('total_score') ?? 0 }}</h2>
                    </div>
                    <div class="icon-circle bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-graph-down-arrow"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. DANH SÁCH CHI TIẾT --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-list-ul me-2 text-primary"></i> Danh sách học sinh nộp bài
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3" width="5%">#</th>
                            <th width="30%">Thông tin học sinh</th>
                            <th width="20%">Thời gian nộp</th>
                            <th class="text-center" width="15%">Điểm số</th>
                            <th class="text-center" width="15%">Xếp loại</th>
                            <th class="text-end pe-4" width="15%">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attempts as $index => $attempt)
                            <tr>
                                <td class="ps-4 fw-bold text-muted">{{ $index + 1 }}</td>
                                
                                {{-- Tên & Email --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center me-3 fw-bold text-primary" style="width: 40px; height: 40px;">
                                            {{ substr($attempt->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $attempt->user->name }}</div>
                                            <div class="small text-muted">{{ $attempt->user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Thời gian nộp --}}
                                <td class="text-muted">
                                    <i class="bi bi-clock me-1"></i> {{ $attempt->created_at->format('H:i d/m/Y') }}
                                </td>

                                {{-- Điểm số --}}
                                <td class="text-center">
                                    <span class="score-badge {{ $attempt->total_score >= 5 ? 'text-primary' : 'text-danger' }}">
                                        {{ $attempt->total_score }}
                                    </span>
                                </td>

                                {{-- Xếp loại --}}
                                <td class="text-center">
                                    @if($attempt->total_score >= 8)
                                        <span class="rank-badge bg-success bg-opacity-10 text-success">Giỏi</span>
                                    @elseif($attempt->total_score >= 6.5)
                                        <span class="rank-badge bg-info bg-opacity-10 text-info">Khá</span>
                                    @elseif($attempt->total_score >= 5)
                                        <span class="rank-badge bg-warning bg-opacity-10 text-warning">Trung bình</span>
                                    @else
                                        <span class="rank-badge bg-danger bg-opacity-10 text-danger">Yếu</span>
                                    @endif
                                </td>

                                {{-- Hành động --}}
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Xem bài <i class="bi bi-arrow-right"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="opacity-25 mb-3">
                                        <i class="bi bi-clipboard-data" style="font-size: 3rem;"></i>
                                    </div>
                                    <h6 class="fw-bold text-secondary">Chưa có dữ liệu</h6>
                                    <p class="text-muted small">Chưa có học sinh nào nộp bài cho đề thi này.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.teacher>