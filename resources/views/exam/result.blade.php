<x-app-layout>
    
    @push('styles')
    <style>
        /* Card điểm số tổng quan */
        .score-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            background: white;
            text-align: center;
            padding: 3rem 1rem;
            position: relative;
        }
        
        .score-circle {
            width: 150px; height: 150px;
            border-radius: 50%;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            margin: 0 auto 20px;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border: 5px solid rgba(255,255,255,0.3);
        }

        /* Màu sắc điểm số */
        .bg-score-high { background: linear-gradient(135deg, #198754 0%, #20c997 100%); } /* >= 8 */
        .bg-score-mid { background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%); } /* >= 5 */
        .bg-score-low { background: linear-gradient(135deg, #dc3545 0%, #f06548 100%); } /* < 5 */

        /* Card câu hỏi sai */
        .wrong-card {
            border: 1px solid #f8d7da;
            border-left: 5px solid #dc3545;
            background: #fff5f5;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            transition: transform 0.2s;
        }
        .wrong-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.1);
        }
    </style>
    @endpush

    {{-- Load Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="row g-4">
        
        {{-- CỘT TRÁI: ĐIỂM SỐ & THÔNG TIN --}}
        <div class="col-lg-4">
            {{-- 1. Card Điểm số --}}
            <div class="score-card mb-4">
                @php
                    $scoreValue = isset($score) && $score !== '' ? (float)$score : 0;
                    if($scoreValue >= 8) {
                        $bgClass = 'bg-score-high';
                        $textClass = 'text-success';
                        $msg = 'Xuất sắc! 🎉';
                        $subMsg = 'Bạn đã nắm vững kiến thức.';
                    } elseif($scoreValue >= 5) {
                        $bgClass = 'bg-score-mid';
                        $textClass = 'text-warning';
                        $msg = 'Khá tốt! 👍';
                        $subMsg = 'Cần cố gắng thêm một chút nữa.';
                    } else {
                        $bgClass = 'bg-score-low';
                        $textClass = 'text-danger';
                        $msg = 'Cần cải thiện 😓';
                        $subMsg = 'Hãy ôn tập lại kiến thức nhé.';
                    }
                @endphp

                <div class="score-circle {{ $bgClass }}">
                    <span class="display-4 fw-bold">{{ number_format($scoreValue, 2) }}</span>
                    <span class="small opacity-75">/ 10 điểm</span>
                </div>

                <h3 class="fw-bold {{ $textClass }}">{{ $msg }}</h3>
                <p class="text-muted">{{ $subMsg }}</p>

                <div class="d-grid gap-2 mt-4 px-4">
                    <a href="{{ route('exam.take', $attemptDetail->exam_session_id ?? 0) }}" class="btn btn-primary rounded-pill fw-bold py-2 shadow-sm">
                        <i class="bi bi-arrow-repeat me-1"></i> Làm lại bài này
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill fw-bold py-2 text-secondary">
                        <i class="bi bi-house me-1"></i> Về trang chủ
                    </a>
                </div>
            </div>

            {{-- 2. Gợi ý cải thiện (Nếu có) --}}
            @if(count($suggestions) > 0)
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-warning bg-opacity-10 border-0 py-3">
                        <h6 class="fw-bold text-warning mb-0"><i class="bi bi-lightbulb-fill me-2"></i> Gợi ý ôn tập</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush small">
                            @foreach($suggestions as $suggest)
                                <li class="list-group-item bg-transparent border-0 ps-0">
                                    <i class="bi bi-check2-circle text-warning me-2"></i> {{ $suggest }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        {{-- CỘT PHẢI: BIỂU ĐỒ & CHI TIẾT CÂU SAI --}}
        <div class="col-lg-8">
            
            {{-- 1. Biểu đồ tiến bộ (Nếu có dữ liệu cũ) --}}
            @if(isset($chartData) && count($chartData) > 1)
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-primary mb-4"><i class="bi bi-graph-up-arrow me-2"></i> Lịch sử tiến bộ</h5>
                        <div style="height: 300px;">
                            <canvas id="progressChart"></canvas>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 2. Chi tiết câu sai --}}
            @php
                $wrongAnswers = $attemptDetail->attemptAnswers->where('is_correct', false);
            @endphp

            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold text-dark mb-0">
                    Chi tiết câu trả lời sai <span class="badge bg-danger rounded-pill fs-6 ms-2">{{ $wrongAnswers->count() }}</span>
                </h4>
            </div>

            @if($wrongAnswers->count() > 0)
                @foreach($wrongAnswers as $ans)
                    <div class="card wrong-card shadow-sm">
                        <div class="card-body p-4">
                            {{-- Header câu hỏi --}}
                            <div class="d-flex justify-content-between mb-3">
                                <span class="badge bg-light text-secondary border">
                                    {{ $ans->question->topic->name ?? 'Kiến thức chung' }}
                                </span>
                                <span class="text-danger fw-bold small"><i class="bi bi-x-circle-fill me-1"></i> Làm sai</span>
                            </div>

                            {{-- Nội dung câu hỏi --}}
                            @if($ans->question->type == 'true_false_item' && $ans->question->parent)
                                <div class="bg-light p-3 rounded-3 mb-3 fst-italic text-secondary small border-start border-3 border-secondary">
                                    {!! nl2br(e(Str::limit($ans->question->parent->content, 200))) !!}
                                </div>
                            @endif
                            
                            <h6 class="fw-bold text-dark mb-4">{{ $ans->question->content }}</h6>

                            {{-- So sánh đáp án --}}
                            <div class="row g-3">
                                {{-- Đáp án của bạn --}}
                                <div class="col-md-6">
                                    <div class="p-3 bg-white border border-danger rounded-3 h-100 position-relative overflow-hidden">
                                        <div class="position-absolute top-0 start-0 bg-danger text-white px-2 py-1 small fw-bold rounded-bottom-end">
                                            Bạn chọn
                                        </div>
                                        <div class="mt-3 text-danger fw-bold">
                                            {{ $ans->selectedAnswer->content ?? 'Không chọn đáp án' }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Đáp án đúng --}}
                                <div class="col-md-6">
                                    <div class="p-3 bg-white border border-success rounded-3 h-100 position-relative overflow-hidden">
                                        <div class="position-absolute top-0 start-0 bg-success text-white px-2 py-1 small fw-bold rounded-bottom-end">
                                            Đáp án đúng
                                        </div>
                                        <div class="mt-3 text-success fw-bold">
                                            @foreach($ans->question->answers as $correctOpt)
                                                @if($correctOpt->is_correct)
                                                    {{ $correctOpt->content }}
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-success border-dashed">
                    <i class="bi bi-trophy-fill text-success" style="font-size: 4rem;"></i>
                    <h4 class="fw-bold text-success mt-3">Tuyệt vời!</h4>
                    <p class="text-muted mb-0">Bạn không làm sai câu nào trong bài thi này.</p>
                </div>
            @endif

        </div>
    </div>

    {{-- SCRIPT VẼ BIỂU ĐỒ --}}
    @if(isset($chartData) && count($chartData) > 1)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('progressChart').getContext('2d');
            const chartData = @json($chartData);

            // Gradient Xanh
            let gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(13, 110, 253, 0.2)');
            gradient.addColorStop(1, 'rgba(13, 110, 253, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(d => d.date),
                    datasets: [{
                        label: 'Điểm số',
                        data: chartData.map(d => d.score),
                        borderColor: '#0d6efd',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: chartData.map(d => d.is_current ? '#dc3545' : '#fff'),
                        pointBorderColor: chartData.map(d => d.is_current ? '#dc3545' : '#0d6efd'),
                        pointRadius: chartData.map(d => d.is_current ? 6 : 4),
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, max: 10, grid: { borderDash: [5, 5] } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
    @endif

</x-app-layout>