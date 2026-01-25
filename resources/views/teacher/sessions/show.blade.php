<x-layouts.teacher title="Giám sát kỳ thi">
    
    @push('styles')
    <style>
        /* --- STYLE HIỆN ĐẠI (CLEAN UI) --- */
        :root { --primary-color: #4f46e5; --text-secondary: #64748b; }

        /* Card & Tabs */
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02); }
        .nav-tabs { border-bottom: 2px solid #e2e8f0; }
        .nav-tabs .nav-link { border: none; color: var(--text-secondary); font-weight: 600; padding: 1rem 1.5rem; transition: all 0.2s; }
        .nav-tabs .nav-link:hover { color: var(--primary-color); background-color: #f8fafc; }
        .nav-tabs .nav-link.active { color: var(--primary-color); border-bottom: 3px solid var(--primary-color); background: transparent; }

        /* Stat Cards Small */
        .stat-card-sm { padding: 1.5rem; border-radius: 12px; text-align: center; transition: transform 0.2s; }
        .stat-card-sm:hover { transform: translateY(-3px); }
        
        /* Progress Bar */
        .progress { height: 8px; border-radius: 4px; background-color: #f1f5f9; }

        /* Table Modern */
        .table-modern thead th { background-color: #f8fafc; color: var(--text-secondary); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; }
        .table-modern td { padding: 1rem 1.5rem; vertical-align: middle; }
    </style>
    @endpush

    {{-- HEADER (ĐÃ XÓA NÚT QUAY LẠI) --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                {{-- Đã xóa link Quay lại ở đây --}}
                <span class="badge bg-light text-dark border">
                    Mã kỳ thi: #{{ $session->id }}
                </span>
            </div>
            
            <h4 class="fw-bold text-dark mb-1">{{ $session->title }}</h4>
            
            <p class="text-muted small mb-0">
                Đề thi gốc: 
                @if($session->exam)
                    <strong class="text-primary">{{ $session->exam->title }}</strong>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger">Đã bị xóa</span>
                @endif
            </p>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('teacher.sessions.edit', $session->id) }}" class="btn btn-white border shadow-sm fw-bold text-secondary">
                <i class="bi bi-gear-fill me-1"></i> Cài đặt
            </a>
            <a href="{{ route('teacher.sessions.export', $session->id) }}" class="btn btn-success fw-bold shadow-sm text-white">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Xuất Excel
            </a>
        </div>
    </div>

    {{-- TABS --}}
    <ul class="nav nav-tabs mb-4" id="monitorTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview">
                <i class="bi bi-speedometer2 me-1"></i> Tổng quan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#students">
                <i class="bi bi-people me-1"></i> Danh sách thí sinh
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#analysis">
                <i class="bi bi-pie-chart me-1"></i> Phân tích câu hỏi
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        {{-- TAB 1: TỔNG QUAN --}}
        <div class="tab-pane fade show active" id="overview">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card stat-card-sm bg-primary bg-opacity-10 text-primary border-0">
                        <h3 class="fw-bold mb-1">{{ $session->attempts->count() }}</h3>
                        <div class="small fw-bold text-uppercase ls-1">Thí sinh tham gia</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card-sm bg-success bg-opacity-10 text-success border-0">
                        <h3 class="fw-bold mb-1">{{ $session->attempts->whereNotNull('submitted_at')->count() }}</h3>
                        <div class="small fw-bold text-uppercase ls-1">Đã nộp bài</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card-sm bg-warning bg-opacity-10 text-warning border-0">
                        <h3 class="fw-bold mb-1">{{ $session->attempts->whereNull('submitted_at')->count() }}</h3>
                        <div class="small fw-bold text-uppercase ls-1">Đang làm bài</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card-sm bg-info bg-opacity-10 text-info border-0">
                        {{-- Tính thời gian còn lại --}}
                        @php
                            $remaining = \Carbon\Carbon::parse($session->end_at)->diffForHumans(null, true);
                            $isExpired = \Carbon\Carbon::now()->gt($session->end_at);
                        @endphp
                        <h3 class="fw-bold mb-1">{{ $isExpired ? 'Đã kết thúc' : $remaining }}</h3>
                        <div class="small fw-bold text-uppercase ls-1">Thời gian còn lại</div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4 p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle me-2"></i> Thông tin chi tiết</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border">
                            <span class="d-block text-muted small text-uppercase">Bắt đầu</span>
                            <strong class="text-dark">{{ \Carbon\Carbon::parse($session->start_at)->format('H:i d/m/Y') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border">
                            <span class="d-block text-muted small text-uppercase">Kết thúc</span>
                            <strong class="text-dark">{{ \Carbon\Carbon::parse($session->end_at)->format('H:i d/m/Y') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border">
                            <span class="d-block text-muted small text-uppercase">Mật khẩu</span>
                            @if($session->password)
                                <span class="badge bg-dark">{{ $session->password }}</span>
                            @else
                                <span class="text-muted fst-italic">Không có</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: DANH SÁCH THÍ SINH --}}
        <div class="tab-pane fade" id="students">
            <div class="card border-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Học sinh</th>
                                <th>Email</th>
                                <th>Vào thi lúc</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Điểm số</th>
                                <th class="text-end pe-4">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($session->attempts as $attempt)
                            <tr>
                                <td class="fw-bold text-dark">{{ $attempt->user->name }}</td>
                                <td class="text-muted">{{ $attempt->user->email }}</td>
                                <td class="text-muted small">{{ $attempt->created_at->format('H:i:s') }}</td>
                                <td class="text-center">
                                    @if($attempt->submitted_at)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Đã nộp</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Đang làm</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold text-primary fs-6">{{ $attempt->total_score ?? '--' }}</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-white border shadow-sm text-primary" title="Xem bài làm">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted">Chưa có thí sinh nào tham gia.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 3: PHÂN TÍCH CÂU HỎI --}}
        <div class="tab-pane fade" id="analysis">
            <div class="card p-4">
                {{-- [SỬA LỖI] Nếu không có exam hoặc không có thống kê --}}
                @if(!$session->exam)
                    <div class="text-center py-5">
                        <div class="text-muted opacity-50 mb-2"><i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i></div>
                        <p class="text-muted small">Đề thi gốc đã bị xóa.<br>Không thể hiển thị thống kê chi tiết từng câu.</p>
                    </div>
                @elseif(empty($questionStats) || count($questionStats) == 0)
                    <div class="text-center py-5">
                        <div class="text-muted opacity-50 mb-2"><i class="bi bi-bar-chart" style="font-size: 2rem;"></i></div>
                        <p class="text-muted small">Chưa có dữ liệu bài làm để phân tích.</p>
                    </div>
                @else
                    <h6 class="fw-bold mb-4">Tỷ lệ làm bài đúng/sai theo từng câu</h6>
                    <div class="row g-4">
                        @foreach($questionStats as $qId => $stat)
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light h-100">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold text-dark text-truncate" style="max-width: 75%;">
                                        Câu {{ $loop->iteration }}: {{ Str::limit(strip_tags($stat['content']), 60) }}
                                    </span>
                                    <span class="badge {{ $stat['ratio'] > 50 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $stat['ratio'] }}% Đúng
                                    </span>
                                </div>
                                <div class="progress mb-2" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stat['ratio'] }}%"></div>
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ 100 - $stat['ratio'] }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span><i class="bi bi-check-circle-fill text-success me-1"></i> {{ $stat['correct'] }} đúng</span>
                                    <span><i class="bi bi-x-circle-fill text-danger me-1"></i> {{ $stat['wrong'] }} sai</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-layouts.teacher>