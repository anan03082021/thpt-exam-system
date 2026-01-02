<x-app-layout>
    
    @push('styles')
    <style>
        /* Banner Gradient đẹp mắt */
        .dashboard-banner {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
            color: white !important;
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.15);
        }
        
        /* Card kỳ thi */
        .exam-card {
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 16px;
            background: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .exam-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
            border-color: rgba(13, 110, 253, 0.1);
        }

        /* Badge trạng thái */
        .status-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 30px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Icon trang trí nền banner */
        .banner-deco {
            position: absolute;
            opacity: 0.1;
            right: -20px;
            bottom: -40px;
            font-size: 10rem;
            color: white;
            transform: rotate(-15deg);
        }
    </style>
    @endpush

    {{-- 1. BANNER CHÀO MỪNG --}}
    <div class="dashboard-banner">
        <div class="row align-items-center position-relative z-1">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-2">Xin chào, {{ Auth::user()->name }}! 👋</h2>
                <p class="mb-0 fs-5 opacity-90">Sẵn sàng chinh phục điểm 10 hôm nay chưa?</p>
            </div>
        </div>
        {{-- Icon trang trí --}}
        <i class="bi bi-mortarboard-fill banner-deco"></i>
    </div>

    {{-- 2. TIÊU ĐỀ --}}
    <div class="d-flex align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center">
            <i class="bi bi-grid-fill text-primary me-2"></i> Danh sách kỳ thi của tôi
        </h4>
    </div>

    {{-- 3. DANH SÁCH --}}
    @if(isset($officialSessions) && $officialSessions->count() > 0)
        <div class="row g-4">
            @foreach($officialSessions as $session)
                @php
                    $now = \Carbon\Carbon::now();
                    $isUpcoming = $now < $session->start_at;
                    $isOngoing = $now >= $session->start_at && $now <= $session->end_at;
                    
                    // Logic màu sắc
                    $borderColor = $isOngoing ? 'border-success' : 'border-warning';
                    $badgeClass = $isOngoing ? 'bg-success text-white' : 'bg-warning text-dark';
                    $statusText = $isOngoing ? 'Đang diễn ra' : 'Sắp diễn ra';
                    $icon = $isOngoing ? 'bi-broadcast' : 'bi-hourglass-split';
                @endphp

                <div class="col-md-6">
                    <div class="card exam-card {{ $isOngoing ? 'border-start border-5 border-success' : 'border-start border-5 border-warning' }}">
                        <div class="card-body p-4 d-flex flex-column">
                            
                            {{-- Header Card --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="status-badge {{ $badgeClass }}">
                                    <i class="bi {{ $icon }} me-1"></i> {{ $statusText }}
                                </span>
                                @if($session->password)
                                    <i class="bi bi-lock-fill text-warning fs-5" title="Yêu cầu mật khẩu"></i>
                                @endif
                            </div>

                            {{-- Content --}}
                            <h5 class="card-title fw-bold text-dark mb-2 text-truncate">{{ $session->title }}</h5>
                            
                            <div class="text-secondary small mb-4">
                                <div class="mb-2"><i class="bi bi-journal-text text-primary me-2"></i> {{ $session->exam->title ?? 'Bài kiểm tra' }}</div>
                                <div><i class="bi bi-clock-history text-primary me-2"></i> {{ $session->exam->duration }} phút</div>
                            </div>

                            {{-- Time Box --}}
                            <div class="bg-light rounded-3 p-3 mb-4 border">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Bắt đầu:</span>
                                    <span class="fw-bold">{{ \Carbon\Carbon::parse($session->start_at)->format('H:i d/m/Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Kết thúc:</span>
                                    <span class="fw-bold text-danger">{{ \Carbon\Carbon::parse($session->end_at)->format('H:i d/m/Y') }}</span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-auto">
                                @if($isUpcoming)
                                    <button disabled class="btn btn-secondary w-100 fw-bold opacity-75">
                                        <i class="bi bi-lock-fill me-2"></i> Chưa đến giờ
                                    </button>
                                @else
                                    <a href="{{ route('exam.take', $session->id) }}" class="btn btn-success w-100 fw-bold shadow-sm">
                                        <i class="bi bi-pencil-fill me-2"></i> VÀO THI NGAY
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="bi bi-calendar2-x text-secondary" style="font-size: 3rem; opacity: 0.3;"></i>
            <h5 class="fw-bold text-secondary mt-3">Chưa có kỳ thi nào</h5>
            <p class="text-muted">Vui lòng quay lại sau hoặc liên hệ giáo viên.</p>
        </div>
    @endif

</x-app-layout>