<x-app-layout>
    
    @push('styles')
    <style>
        /* Banner Gradient Tím/Xanh */
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
            background: #0d6efd; /* Xanh dương */
            color: white;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }

        .nav-btn.active-practice {
            background: #198754; /* Xanh lá */
            color: white;
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
        }

        /* Bảng */
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

    {{-- 1. LOAD THƯ VIỆN CHART.JS VÀ PLUGIN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    {{-- 2. BANNER TIÊU ĐỀ --}}
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
        <div class="position-absolute bg-white opacity-10 rounded-circle" style="width: 200px; height: 200px; top: -50px; right: -50px;"></div>
    </div>

    {{-- 3. NỘI DUNG CHÍNH (ALPINEJS) --}}
    <div x-data="{ activeTab: 'official' }">
        
        {{-- BIỂU ĐỒ --}}
        <div class="row g-4 mb-5">
            {{-- Biểu đồ tròn: Độ phủ --}}
            <div class="col-md-4">
                <div class="stats-card p-4 d-flex flex-column align-items-center justify-content-center text-center">
                    <h5 class="fw-bold text-secondary mb-4"><i class="bi bi-pie-chart-fill me-2 text-warning"></i> Độ phủ kiến thức</h5>
                    
                    <div style="width: 100%; height: 250px; position: relative;">
                        <canvas id="progressChart"></canvas>
                    </div>

                    <div class="mt-2 text-muted small bg-light px-3 py-2 rounded-pill">
                        Tổng số đề có trong kho: <strong>{{ $totalExamsAvailable }}</strong>
                    </div>
                </div>
            </div>

            {{-- Biểu đồ Radar: Mức độ thành thạo --}}
            <div class="col-md-8">
                <div class="stats-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-secondary mb-0">
                            <i class="bi bi-radar me-2 text-primary"></i> Mức độ thành thạo theo chủ đề
                        </h5>
                        <span class="badge bg-light text-muted border">Thang điểm 100%</span>
                    </div>
                    
                    <div style="height: 300px; width: 100%; display: flex; justify-content: center;">
                        <canvas id="topicRadarChart"></canvas>
                    </div>
                    
                    <div class="mt-3 text-center small text-muted">
                        <span class="me-3"><i class="bi bi-circle-fill" style="color: #6366f1;"></i> Năng lực hiện tại</span>
                        <span><i class="bi bi-circle-fill text-secondary opacity-25"></i> Mức tối đa</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- DANH SÁCH LỊCH SỬ --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">Chi tiết lịch sử</h4>
            
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
                                            <a href="{{ route('student.exam.result.official', $attempt->id) }}" 
                                               class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-3 transition-hover">
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
                                        </td>

                                        <td class="text-end pe-4">
                                            <a href="{{ route('student.exam.result.practice', $item['latest_id']) }}" 
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

    {{-- 4. SCRIPT VẼ BIỂU ĐỒ (ĐÃ SỬA LỖI CÚ PHÁP) --}}
    {{-- 4. SCRIPT VẼ BIỂU ĐỒ (ĐÃ SỬA LỖI CÚ PHÁP) --}}
{{-- 4. SCRIPT VẼ BIỂU ĐỒ (PHIÊN BẢN AN TOÀN TUYỆT ĐỐI) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // A. KHỞI TẠO DỮ LIỆU TỪ PHP SANG JS MỘT CÁCH AN TOÀN
            // Chúng ta dùng json_encode để chuyển đổi dữ liệu PHP sang JSON chuẩn
            const chartData = {
                pie: {
                    done: {{ $examsTakenCount }},
                    total: {{ $totalExamsAvailable }}
                },
                radar: {
                    labels: {!! json_encode($chartLabels) !!},
                    percents: {!! json_encode($chartPercent) !!},
                    raws: {!! json_encode($chartRaw) !!}
                }
            };

            // Đăng ký Plugin
            if (typeof ChartDataLabels !== 'undefined') {
                Chart.register(ChartDataLabels);
            }

            // ==========================================
            // B. VẼ BIỂU ĐỒ TRÒN
            // ==========================================
            const ctxPie = document.getElementById('progressChart');
            if (ctxPie) {
                const valDone = chartData.pie.done;
                const valTotal = chartData.pie.total;
                let valRem = Math.max(0, valTotal - valDone);

                new Chart(ctxPie.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: ['Đã hoàn thành', 'Chưa hoàn thành'],
                        datasets: [{
                            data: [valDone, valRem],
                            backgroundColor: ['#6366f1', '#e2e8f0'], // Tím Indigo & Xám
                            borderColor: '#ffffff',
                            borderWidth: 3,
                            hoverOffset: 6
                        }]
                    },
                    options: { 
                        responsive: true, maintainAspectRatio: false, layout: { padding: 25 },
                        plugins: { 
                            legend: { display: false }, tooltip: { enabled: false },
                            datalabels: {
                                color: ctx => ctx.dataIndex === 0 ? '#ffffff' : '#475569',
                                anchor: 'end', align: 'start', offset: 0,
                                font: { weight: 'bold', size: 12, family: "'Plus Jakarta Sans', sans-serif" },
                                formatter: (val, ctx) => {
                                    if(val <= 0) return '';
                                    let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    let pct = (sum > 0) ? (val * 100 / sum).toFixed(0) + "%" : "0%";
                                    return val + ' đề\n(' + pct + ')';
                                }
                            }
                        } 
                    }
                });
            }

            // ==========================================
            // C. VẼ BIỂU ĐỒ RADAR
            // ==========================================
            const ctxRadar = document.getElementById('topicRadarChart');
            if (ctxRadar) {
                // Lấy dữ liệu từ biến chartData đã khai báo ở trên
                const radarLabels = chartData.radar.labels.length ? chartData.radar.labels : ['Chưa có dữ liệu'];
                const radarPercents = chartData.radar.labels.length ? chartData.radar.percents : [0];
                const radarRaws = chartData.radar.labels.length ? chartData.radar.raws : ['0/0'];
                
                const isAllZero = radarPercents.every(v => v === 0);
                const suggestedMax = isAllZero ? 10 : 100;

                new Chart(ctxRadar.getContext('2d'), {
                    type: 'radar',
                    data: {
                        labels: radarLabels,
                        datasets: [{
                            label: 'Tỷ lệ làm đúng',
                            data: radarPercents,
                            fill: true,
                            backgroundColor: 'rgba(99, 102, 241, 0.2)',
                            borderColor: '#6366f1',
                            pointBackgroundColor: '#6366f1',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '#6366f1',
                            borderWidth: 2, pointRadius: 3, pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }, datalabels: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(30, 27, 75, 0.9)',
                                padding: 10, bodyFont: { size: 13 }, displayColors: false,
                                callbacks: {
                                    title: ctx => ctx[0].label,
                                    label: ctx => ` ✅ Đúng: ${radarRaws[ctx.dataIndex] || '0/0'} câu (${ctx.raw}%)`
                                }
                            }
                        },
                        scales: {
                            r: {
                                angleLines: { color: '#f1f5f9' }, grid: { color: '#f1f5f9' },
                                pointLabels: { 
                                    font: { size: 10, weight: '600', family: "'Plus Jakarta Sans', sans-serif" }, 
                                    color: '#64748b',
                                    callback: label => (label.length > 15 ? label.match(/.{1,15}(\s|$)/g) : label)
                                },
                                suggestedMin: 0, suggestedMax: suggestedMax,
                                ticks: { stepSize: 20, display: false, backdropColor: 'transparent' }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>