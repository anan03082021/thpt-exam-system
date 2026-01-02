<x-app-layout>
    
    @push('styles')
    <style>
        /* Banner Gradient Tím/Xanh - Tạo cảm giác Tổng kết/Thống kê */
        .history-banner {
            background: linear-gradient(135deg, #6f42c1 0%, #8553e6 100%);
            color: white;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(111, 66, 193, 0.2);
        }

        /* Card thống kê */
        .stats-card {
            border: none;
            border-radius: 16px;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
            height: 100%;
        }

        /* Tabs chuyển đổi */
        .nav-pills-custom {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 12px;
            display: inline-flex;
            gap: 0.5rem;
        }
        
        .nav-btn {
            border: none;
            background: transparent;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        
        .nav-btn:hover {
            background: #e9ecef;
            color: #495057;
        }

        .nav-btn.active-official {
            background: #0d6efd; /* Xanh dương cho Kỳ thi */
            color: white;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }

        .nav-btn.active-practice {
            background: #198754; /* Xanh lá cho Luyện tập */
            color: white;
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
        }

        /* Bảng đẹp hơn */
        .table-custom th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
        .table-custom td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }
    </style>
    @endpush

    {{-- Load Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- 1. BANNER TIÊU ĐỀ --}}
    <div class="history-banner">
        <div class="row align-items-center position-relative z-1">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-2">Tiến độ học tập 📊</h2>
                <p class="mb-0 fs-5 opacity-90">Theo dõi quá trình phát triển và lịch sử làm bài của bạn.</p>
            </div>
            <div class="col-lg-4 text-end d-none d-lg-block">
                <i class="bi bi-graph-up-arrow" style="font-size: 5rem; opacity: 0.2;"></i>
            </div>
        </div>
        {{-- Họa tiết nền --}}
        <div class="position-absolute bg-white opacity-10 rounded-circle" style="width: 200px; height: 200px; top: -50px; right: -50px;"></div>
    </div>

    {{-- Dùng AlpineJS để quản lý Tab --}}
    <div x-data="{ activeTab: 'official' }">
        
        {{-- PHẦN 1: BIỂU ĐỒ TỔNG QUAN --}}
        <div class="row g-4 mb-5">
            {{-- Biểu đồ tròn: Độ phủ --}}
            <div class="col-md-4">
                <div class="stats-card p-4 d-flex flex-column align-items-center justify-content-center text-center">
                    <h5 class="fw-bold text-secondary mb-4"><i class="bi bi-pie-chart-fill me-2 text-warning"></i> Độ phủ kiến thức</h5>
                    <div style="width: 180px; height: 180px; position: relative;">
                        <canvas id="progressChart"></canvas>
                    </div>
                    <div class="mt-4 text-muted small bg-light px-3 py-2 rounded-pill">
                        Đã làm <strong>{{ $examsTakenCount }}</strong> / {{ $totalExamsAvailable }} đề có sẵn
                    </div>
                </div>
            </div>

            {{-- Biểu đồ cột: Điểm số --}}
            <div class="col-md-8">
                <div class="stats-card p-4">
                    <h5 class="fw-bold text-secondary mb-4"><i class="bi bi-bar-chart-line-fill me-2 text-primary"></i> Phổ điểm Kỳ thi chính thức</h5>
                    <div style="height: 250px; width: 100%;">
                        <canvas id="examBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- PHẦN 2: DANH SÁCH LỊCH SỬ (TABS) --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">Chi tiết lịch sử</h4>
            
            {{-- Bộ nút chuyển Tab --}}
            <div class="nav-pills-custom shadow-sm">
                <button @click="activeTab = 'official'" 
                    :class="activeTab === 'official' ? 'active-official' : ''"
                    class="nav-btn d-flex align-items-center">
                    <i class="bi bi-card-checklist me-2"></i> Kỳ thi chính thức
                </button>
                
                <button @click="activeTab = 'practice'" 
                    :class="activeTab === 'practice' ? 'active-practice' : ''"
                    class="nav-btn d-flex align-items-center">
                    <i class="bi bi-lightning-charge me-2"></i> Luyện tập
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            
            {{-- Tab 1: KỲ THI CHÍNH THỨC --}}
            <div x-show="activeTab === 'official'" x-transition.opacity>
                @if($examAttempts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-custom table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tên Kỳ thi</th>
                                    <th>Ngày thi</th>
                                    <th>Điểm số</th>
                                    <th class="text-end pe-4">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($examAttempts as $attempt)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $attempt->examSession->title ?? 'Bài thi không tồn tại' }}</div>
                                            <div class="small text-muted">Mã: #{{ $attempt->id }}</div>
                                        </td>
                                        <td class="text-secondary small">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ \Carbon\Carbon::parse($attempt->submitted_at)->format('H:i d/m/Y') }}
                                        </td>
                                        <td>
                                            @php
                                                $score = $attempt->total_score ?? 0;
                                                $badgeClass = $score >= 8 ? 'bg-success' : ($score >= 5 ? 'bg-primary' : 'bg-danger');
                                            @endphp
                                            <span class="badge {{ $badgeClass }} bg-opacity-10 text-{{ $score >= 8 ? 'success' : ($score >= 5 ? 'primary' : 'danger') }} px-3 py-2 rounded-pill fw-bold" style="font-size: 0.9rem;">
                                                {{ number_format($score, 2) }} đ
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('exam.result', $attempt->id) }}" class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-3">
                                                Xem kết quả <i class="bi bi-arrow-right-short"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-secondary fs-1 opacity-25"></i>
                        <p class="text-muted mt-2">Chưa có dữ liệu kỳ thi chính thức nào.</p>
                    </div>
                @endif
            </div>

            {{-- Tab 2: LUYỆN TẬP --}}
            <div x-show="activeTab === 'practice'" x-transition.opacity style="display: none;">
                @if(count($practiceHistory) > 0)
                    <div class="table-responsive">
                        <table class="table table-custom table-hover mb-0">
                            <thead class="bg-success bg-opacity-10">
                                <tr>
                                    <th class="ps-4 text-success">Tên Đề mẫu</th>
                                    <th class="text-center text-success">Số lần làm</th>
                                    <th class="text-center text-success">Thành tích (Cao nhất)</th>
                                    <th class="text-end pe-4 text-success">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($practiceHistory as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $item['title'] }}</div>
                                            <div class="small text-muted">
                                                <i class="bi bi-clock-history me-1"></i>
                                                Mới nhất: {{ \Carbon\Carbon::parse($item['latest_at'])->format('H:i d/m/Y') }}
                                            </div>
                                        </td>
                                        
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border rounded-pill px-3">
                                                {{ $item['count'] }} lần
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <div class="fw-bold {{ $item['best_score'] >= 8 ? 'text-success' : ($item['best_score'] >= 5 ? 'text-primary' : 'text-danger') }}" style="font-size: 1.1rem;">
                                                {{ number_format($item['best_score'], 2) }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                TB: {{ number_format($item['average_score'], 2) }}
                                            </div>
                                        </td>

                                        <td class="text-end pe-4">
                                            <a href="{{ route('exam.result', $item['latest_id']) }}" 
                                               class="btn btn-sm btn-outline-success rounded-pill fw-bold px-3">
                                                <i class="bi bi-bar-chart-fill me-1"></i> Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-journal-x text-secondary fs-1 opacity-25"></i>
                        <p class="text-muted mt-2 mb-3">Bạn chưa làm bài luyện tập nào.</p>
                        <a href="{{ route('student.practice') }}" class="btn btn-success rounded-pill px-4">Luyện tập ngay</a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Script vẽ biểu đồ (Logic giữ nguyên, chỉ chỉnh màu cho khớp theme Bootstrap) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Biểu đồ Tròn
            const ctxPie = document.getElementById('progressChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: ['Đã làm', 'Chưa làm'],
                    datasets: [{
                        data: [{{ $examsTakenCount }}, {{ $totalExamsAvailable - $examsTakenCount }}],
                        // Màu Vàng (Warning) và Xám nhạt
                        backgroundColor: ['#ffc107', '#e9ecef'], 
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: { 
                    cutout: '75%', 
                    borderRadius: 20, 
                    plugins: { legend: { display: false } } 
                }
            });

            // 2. Biểu đồ Cột
            const ctxBar = document.getElementById('examBarChart').getContext('2d');
            const barData = @json($barChartData);
            
            // Gradient Xanh dương (Primary)
            let barGradient = ctxBar.createLinearGradient(0, 0, 0, 300);
            barGradient.addColorStop(0, '#0d6efd'); // Blue bootstrap
            barGradient.addColorStop(1, '#a6c8ff'); // Light blue

            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: barData.map(i => i.label),
                    datasets: [{ 
                        label: 'Điểm', 
                        data: barData.map(i => i.score), 
                        backgroundColor: barGradient,
                        borderRadius: 8,
                        barThickness: 30,
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    scales: { 
                        y: { 
                            beginAtZero: true, 
                            max: 10, 
                            grid: { borderDash: [5, 5], color: '#e9ecef' } 
                        },
                        x: { 
                            grid: { display: false } 
                        }
                    }, 
                    plugins: { legend: { display: false } } 
                }
            });
        });
    </script>
</x-app-layout>