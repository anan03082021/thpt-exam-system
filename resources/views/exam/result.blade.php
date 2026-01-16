<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }

        /* --- HERO CARD --- */
        .hero-card {
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%); /* Màu xanh dương sáng hơn cho Luyện tập */
            border-radius: 24px; color: white; padding: 2.5rem;
            position: relative; overflow: hidden;
            box-shadow: 0 20px 40px -10px rgba(14, 165, 233, 0.4);
            min-height: 320px;
            display: flex; flex-direction: column; justify-content: center;
        }
        .hero-pattern {
            position: absolute; top: 0; right: 0; width: 300px; height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .score-huge { font-size: 4.5rem; font-weight: 800; line-height: 1; letter-spacing: -2px; }
        
        /* --- GLASS CARD --- */
        .glass-card {
            background: white; border-radius: 20px; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); padding: 1.5rem; height: 100%;
            display: flex; flex-direction: column; justify-content: center;
        }

        /* --- QUESTION ITEM --- */
        .q-wrapper {
            background: white; border-radius: 16px; margin-bottom: 2rem;
            border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            overflow: hidden;
        }

        /* Status Styles */
        .status-wrong { border-left: 6px solid #ef4444; }
        .bg-wrong-light { background-color: #fef2f2; }
        .text-wrong { color: #ef4444; }

        .status-correct { border-left: 6px solid #10b981; }
        .bg-correct-light { background-color: #ecfdf5; }
        .text-correct { color: #10b981; }

        /* --- ANSWER BOX STYLES --- */
        .ans-box {
            padding: 12px 16px; border-radius: 10px; border: 1px solid #e2e8f0;
            margin-bottom: 8px; background: #fff;
            display: flex; align-items: center; justify-content: space-between;
            font-weight: 500; color: #64748b; transition: all 0.2s;
        }
        .ans-normal { background: #fff; opacity: 0.7; }
        .ans-user-correct { background: #dcfce7; border-color: #86efac; color: #166534; box-shadow: 0 2px 4px rgba(22, 101, 52, 0.1); }
        .ans-user-wrong { background: #fee2e2; border-color: #fecaca; color: #991b1b; }
        .ans-missed-correct { background: #fff; border: 2px solid #22c55e; color: #15803d; }

        /* Tip Box */
        .tip-box {
            background: #fffbeb; border: 1px dashed #f59e0b; border-radius: 12px;
            padding: 1rem 1.5rem; margin-top: 1.5rem; color: #92400e;
            display: flex; align-items: start; gap: 1rem;
        }
    </style>
    @endpush

    @php
        // Eager load dữ liệu cần thiết để tránh N+1 Query
        $rawAnswers = $attemptDetail->attemptAnswers->load(['question.parent', 'question.answers', 'question.topic', 'question.coreContent', 'question.learningObjective']);

        $groupedQuestions = $rawAnswers->groupBy(function($ans) {
            return $ans->question->parent_id ?? $ans->question_id;
        });

        // Tự động phát hiện môn chọn (CS/ICT) để lọc
        $userElective = null;
        foreach($groupedQuestions as $group) {
            $qOri = strtolower(trim($group->first()->question->orientation ?? ''));
            if ($qOri === 'cs' || $qOri === 'ict') {
                $userElective = $qOri;
                break;
            }
        }

        // SẮP XẾP: Câu SAI lên đầu
        $sortedQuestions = $groupedQuestions->sortBy(function($group) {
            return $group->contains('is_correct', false) ? 0 : 1;
        });
    @endphp

    <div class="container py-4">
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="text-uppercase text-muted fw-bold small tracking-wider mb-1">Kết quả Luyện tập</div>
                <h3 class="fw-bold text-dark m-0">{{ $exam->title }}</h3>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('student.history') }}" class="btn btn-white bg-white border fw-bold shadow-sm rounded-pill px-4">
                    <i class="bi bi-clock-history me-2"></i> Lịch sử
                </a>
                {{-- Nút làm lại bài --}}
                <a href="{{ route('exam.practice', $exam->id) }}" class="btn btn-primary fw-bold shadow-sm rounded-pill px-4">
                    <i class="bi bi-arrow-repeat me-2"></i> Làm lại
                </a>
            </div>
        </div>

        {{-- TOP SECTION: SCORE & AREA CHART --}}
        <div class="row g-4 mb-5 justify-content-center">
            
            {{-- Cột Điểm Số (Hero) --}}
            <div class="col-lg-5">
                <div class="hero-card">
                    <div class="hero-pattern"></div>
                    <div class="text-white text-opacity-75 fw-bold text-uppercase small mb-2">Điểm lần thi này</div>
                    <div class="d-flex align-items-baseline gap-3 mb-4">
                        <div class="score-huge">{{ number_format($score, 2) }}</div>
                        <div class="fs-4 text-white text-opacity-50">/ 10</div>
                    </div>
                    
                    <div class="progress bg-white bg-opacity-20 rounded-pill mb-4" style="height: 8px;">
                        <div class="progress-bar bg-white rounded-pill" role="progressbar" style="width: {{ $score * 10 }}%"></div>
                    </div>

                    {{-- Nhận xét ngắn --}}
                    <div class="d-flex align-items-center gap-2 text-white">
                        @if($score >= 8)
                            <i class="bi bi-trophy-fill fs-5"></i> <span class="fw-bold">Làm rất tốt! Hãy duy trì phong độ.</span>
                        @elseif($score >= 5)
                            <i class="bi bi-emoji-smile-fill fs-5"></i> <span class="fw-bold">Đạt yêu cầu, nhưng cần cải thiện thêm.</span>
                        @else
                            <i class="bi bi-emoji-frown-fill fs-5"></i> <span class="fw-bold">Cần ôn tập kỹ hơn các kiến thức hổng.</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cột Biểu Đồ Miền (Area Chart) --}}
            <div class="col-lg-auto">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Tiến bộ qua các lần thi</h6>
                    </div>
                    
                    {{-- Khung chứa biểu đồ --}}
                    <div style="width: 420px; height: 240px;"> 
                        @if(isset($chartData) && count($chartData) > 0)
                            <canvas id="progressChart"></canvas>
                        @else
                            <div class="h-100 d-flex align-items-center justify-content-center text-muted small">
                                Chưa có đủ dữ liệu lịch sử
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- MIDDLE SECTION: CHI TIẾT BÀI LÀM --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <h4 class="fw-bold text-dark m-0">Chi tiết bài làm</h4>
            <div class="h-1px bg-secondary opacity-25 flex-grow-1"></div>
        </div>

        @foreach($sortedQuestions as $groupId => $group)
            @php
                $firstItem = $group->first();
                $mainQuestion = $firstItem->question->parent ?? $firstItem->question;
                
                // Lọc môn không chọn
                $ori = strtolower(trim($mainQuestion->orientation ?? ''));
                if ($ori !== 'chung' && $ori !== '' && $userElective && $ori !== $userElective) {
                    continue; 
                }

                $hasWrong = $group->contains('is_correct', false);
                $statusClass = $hasWrong ? 'status-wrong' : 'status-correct';
                $headerBg = $hasWrong ? 'bg-wrong-light' : 'bg-correct-light';
                $statusText = $hasWrong ? 'Cần ôn tập' : 'Chính xác';
                $statusTextColor = $hasWrong ? 'text-wrong' : 'text-correct';
                $statusIcon = $hasWrong ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill';
            @endphp

            <div class="q-wrapper {{ $statusClass }}">
                {{-- Header --}}
                <div class="p-3 {{ $headerBg }} border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-white text-dark border shadow-sm px-3 py-2 rounded-pill fw-bold">Câu {{ $loop->iteration }}</span>
                        <span class="badge bg-white text-secondary border px-3 py-2 rounded-pill fw-normal">
                            {{ $mainQuestion->topic->name ?? 'Tổng hợp' }}
                        </span>
                    </div>
                    <div class="fw-bold {{ $statusTextColor }} d-flex align-items-center gap-2">
                        {{ $statusText }} <i class="bi {{ $statusIcon }}"></i>
                    </div>
                </div>

                <div class="p-4">
                    <div class="fs-5 fw-bold text-dark mb-4 lh-base">
                        {!! $mainQuestion->content !!}
                    </div>

                    {{-- TRƯỜNG HỢP 1: TRẮC NGHIỆM ĐƠN --}}
                    @if($mainQuestion->type == 'single_choice')
                        <div class="d-flex flex-column gap-2">
                            @php
                                $userAns = $group->first();
                                $userSelectedId = $userAns->selected_answer_id;
                            @endphp

                            @foreach($mainQuestion->answers as $opt)
                                @php
                                    $isCorrect = $opt->is_correct;
                                    $isSelected = ($opt->id == $userSelectedId);
                                    
                                    $boxClass = 'ans-normal';
                                    $icon = '<i class="bi bi-circle me-2"></i>';
                                    $badge = '';

                                    if ($isSelected) {
                                        if ($isCorrect) {
                                            $boxClass = 'ans-user-correct';
                                            $icon = '<i class="bi bi-check-circle-fill me-2"></i>';
                                            $badge = '<span class="badge bg-success text-white ms-auto">Bạn chọn</span>';
                                        } else {
                                            $boxClass = 'ans-user-wrong';
                                            $icon = '<i class="bi bi-x-circle-fill me-2"></i>';
                                            $badge = '<span class="badge bg-danger text-white ms-auto">Bạn chọn</span>';
                                        }
                                    } elseif ($isCorrect) {
                                        $boxClass = 'ans-missed-correct';
                                        $icon = '<i class="bi bi-check-circle-fill me-2"></i>';
                                        $badge = '<span class="badge bg-success bg-opacity-10 text-success border border-success ms-auto">Đáp án đúng</span>';
                                    }
                                @endphp

                                <div class="ans-box {{ $boxClass }}">
                                    <div class="d-flex align-items-center w-100">
                                        <span class="me-2">{!! $icon !!}</span>
                                        <span class="flex-grow-1">{{ $opt->content }}</span>
                                        {!! $badge !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    {{-- TRƯỜNG HỢP 2: ĐÚNG/SAI CHÙM --}}
                    @elseif($mainQuestion->type == 'true_false_group')
                        <div class="table-responsive border rounded-3">
                            <table class="table mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">Ý nhận định</th>
                                        <th class="text-center" width="15%">Bạn chọn</th>
                                        <th class="text-center" width="15%">Đáp án</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group as $ans)
                                        @php
                                            $correctOpt = $ans->question->answers->where('is_correct', true)->first();
                                            $correctText = $correctOpt ? $correctOpt->content : '-';
                                            $userText = $ans->selectedAnswer->content ?? '-';
                                            $isRowCorrect = $ans->is_correct;
                                            $rowBg = $isRowCorrect ? '' : 'bg-danger bg-opacity-10';
                                        @endphp
                                        <tr class="{{ $rowBg }}">
                                            <td class="ps-3">{{ $ans->question->content }}</td>
                                            <td class="text-center fw-bold {{ $isRowCorrect ? 'text-success' : 'text-danger' }}">
                                                {{ $userText }}
                                            </td>
                                            <td class="text-center fw-bold text-primary">
                                                {{ $correctText }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- GỢI Ý ÔN TẬP (CÂU VĂN LIỀN MẠCH) --}}
                    @if($hasWrong)
                        <div class="tip-box">
                            <i class="bi bi-lightbulb-fill fs-4 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Gợi ý ôn tập:</h6>
                                <p class="mb-0 lh-base">
                                    Bạn nên ôn lại kiến thức 
                                    <strong class="text-dark">Lớp {{ $mainQuestion->grade }}</strong>, 
                                    thuộc chủ đề <strong class="text-dark">{{ $mainQuestion->topic->name ?? '...' }}</strong>
                                    @if($mainQuestion->coreContent)
                                        , tập trung vào nội dung <strong class="text-dark">{{ $mainQuestion->coreContent->name }}</strong>
                                    @endif
                                    @if($mainQuestion->learningObjective)
                                        để nắm vững yêu cầu <em class="text-dark">"{{ $mainQuestion->learningObjective->content }}"</em>
                                    @endif.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- CHART SCRIPT: BIỂU ĐỒ MIỀN (AREA CHART) --}}
    @if(isset($chartData) && count($chartData) > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('progressChart').getContext('2d');
            const data = @json($chartData); // Dữ liệu từ Controller

            // Tạo Gradient cho phần nền (Fill)
            const gradientFill = ctx.createLinearGradient(0, 0, 0, 300);
            gradientFill.addColorStop(0, 'rgba(14, 165, 233, 0.5)'); // Xanh đậm ở trên
            gradientFill.addColorStop(1, 'rgba(14, 165, 233, 0.0)'); // Trong suốt ở dưới

            new Chart(ctx, {
                type: 'line', // Biểu đồ đường
                data: {
                    labels: data.map(d => d.date), // Trục X: Ngày tháng
                    datasets: [{
                        label: 'Điểm số',
                        data: data.map(d => d.score), // Trục Y: Điểm
                        borderColor: '#0ea5e9',       // Màu đường kẻ (Xanh dương)
                        backgroundColor: gradientFill, // Màu nền gradient (Tạo hiệu ứng Area)
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0ea5e9',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true, // [QUAN TRỌNG] Bật chế độ Area Chart
                        tension: 0.4 // [QUAN TRỌNG] Làm mềm đường cong
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }, // Ẩn chú thích vì chỉ có 1 đường
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Điểm: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 10,
                            grid: { borderDash: [5, 5], color: '#f1f5f9' },
                            ticks: { font: { family: "'Plus Jakarta Sans', sans-serif" } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: "'Plus Jakarta Sans', sans-serif" }, maxRotation: 45, minRotation: 0 }
                        }
                    }
                }
            });
        });
    </script>
    @endif

</x-app-layout>