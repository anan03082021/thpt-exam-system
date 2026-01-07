<x-layouts.teacher title="Giám sát kỳ thi">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary">{{ $session->title }}</h4>
            <span class="badge bg-light text-dark border mt-2">
                Mã kỳ thi: #{{ $session->id }} | Đề thi: {{ $session->exam->title }}
            </span>
        </div>
        <div>
            <a href="{{ route('teacher.sessions.edit', $session->id) }}" class="btn btn-outline-primary btn-sm me-2">
                <i class="bi bi-pencil-square"></i> Chỉnh sửa
            </a>
            <a href="{{ route('teacher.sessions.export', $session->id) }}" class="btn btn-success btn-sm fw-bold">
                <i class="bi bi-file-earmark-excel"></i> Xuất Excel
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4" id="monitorTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#overview">
                <i class="bi bi-speedometer2 me-1"></i> Tổng quan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#students">
                <i class="bi bi-people me-1"></i> Danh sách thí sinh
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#analysis">
                <i class="bi bi-pie-chart me-1"></i> Phân tích câu hỏi
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        {{-- TAB 1: TỔNG QUAN --}}
        <div class="tab-pane fade show active" id="overview">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-primary bg-opacity-10 text-primary text-center">
                        <h3>{{ $session->attempts->count() }}</h3>
                        <div class="small fw-bold">Thí sinh tham gia</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-success bg-opacity-10 text-success text-center">
                        <h3>{{ $session->attempts->whereNotNull('submitted_at')->count() }}</h3>
                        <div class="small fw-bold">Đã nộp bài</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-warning bg-opacity-10 text-warning text-center">
                        <h3>{{ $session->attempts->whereNull('submitted_at')->count() }}</h3>
                        <div class="small fw-bold">Đang làm bài</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-info bg-opacity-10 text-info text-center">
                        {{-- Tính thời gian còn lại (Demo) --}}
                        @php
                            $remaining = \Carbon\Carbon::parse($session->end_at)->diffForHumans(null, true);
                            $isExpired = \Carbon\Carbon::now()->gt($session->end_at);
                        @endphp
                        <h3>{{ $isExpired ? 'Đã kết thúc' : $remaining }}</h3>
                        <div class="small fw-bold">Thời gian còn lại</div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4 shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold">Thông tin chi tiết</h6>
                    <ul class="list-unstyled mb-0">
                        <li><strong>Bắt đầu:</strong> {{ \Carbon\Carbon::parse($session->start_at)->format('H:i d/m/Y') }}</li>
                        <li><strong>Kết thúc:</strong> {{ \Carbon\Carbon::parse($session->end_at)->format('H:i d/m/Y') }}</li>
                        <li><strong>Mật khẩu:</strong> <span class="badge bg-secondary">{{ $session->password ?? 'Không có' }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- TAB 2: DANH SÁCH THÍ SINH (CÓ TÊN & EMAIL) --}}
        <div class="tab-pane fade" id="students">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Họ và tên</th>
                                <th>Email</th>
                                <th>Vào thi lúc</th>
                                <th>Trạng thái</th>
                                <th>Điểm số</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($session->attempts as $attempt)
                            <tr>
                                <td class="fw-bold">{{ $attempt->user->name }}</td>
                                <td class="text-muted small">{{ $attempt->user->email }}</td>
                                <td>{{ $attempt->created_at->format('H:i:s') }}</td>
                                <td>
                                    @if($attempt->submitted_at)
                                        <span class="badge bg-success">Đã nộp</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Đang làm</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-primary">{{ $attempt->total_score ?? '--' }}</td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-light border" title="Xem bài làm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 3: PHÂN TÍCH CÂU HỎI --}}
        <div class="tab-pane fade" id="analysis">
            <div class="card border-0 shadow-sm p-4">
                <h6 class="fw-bold mb-3">Tỷ lệ làm bài đúng/sai theo từng câu</h6>
                
                @foreach($questionStats as $qId => $stat)
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold text-dark text-truncate" style="max-width: 70%;">
                            Câu {{ $loop->iteration }}: {{ Str::limit(strip_tags($stat['content']), 80) }}
                        </span>
                        <span class="small fw-bold">{{ $stat['ratio'] }}% Đúng</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stat['ratio'] }}%"></div>
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ 100 - $stat['ratio'] }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1 small text-muted">
                        <span>Đúng: {{ $stat['correct'] }}</span>
                        <span>Sai: {{ $stat['wrong'] }}</span>
                    </div>
                </div>
                @endforeach

                @if(count($questionStats) == 0)
                    <div class="text-center text-muted py-5">Chưa có dữ liệu phân tích</div>
                @endif
            </div>
        </div>

    </div>
</x-layouts.teacher>