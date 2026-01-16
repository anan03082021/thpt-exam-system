<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }

        /* --- HERO CARD --- */
        .hero-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 24px; color: white; padding: 2.5rem;
            position: relative; overflow: hidden;
            box-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.4);
            /* Đảm bảo chiều cao tối thiểu để cân đối với biểu đồ */
            min-height: 320px; 
            display: flex; flex-direction: column; justify-content: center;
        }
        .hero-pattern {
            position: absolute; top: 0; right: 0; width: 300px; height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .score-huge { font-size: 4.5rem; font-weight: 800; line-height: 1; letter-spacing: -2px; }
        
        /* --- GLASS CARD (Đã chỉnh sửa để ôm sát) --- */
        .glass-card {
            background: white; border-radius: 20px; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); 
            /* [QUAN TRỌNG] Tự động co giãn theo nội dung */
            width: fit-content; 
            height: auto;
            margin: 0 auto; /* Căn giữa */
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

    <div class="container py-4">
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <div class="text-uppercase text-muted fw-bold small tracking-wider mb-1">Kết quả thi chính thức</div>
                <h3 class="fw-bold text-dark m-0">{{ $attempt->examSession->title }}</h3>
            </div>
            <a href="{{ route('student.history') }}" class="btn btn-white bg-white border fw-bold shadow-sm rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i> Trở về
            </a>
        </div>

        {{-- TOP SECTION: Căn giữa row để 2 khối nằm gọn gàng --}}
        <div class="row g-4 mb-5 justify-content-center">
            
            {{-- Cột Điểm Số (Hero) --}}
            <div class="col-lg-5">
                <div class="hero-card">
                    <div class="hero-pattern"></div>
                    <div class="text-white text-opacity-75 fw-bold text-uppercase small mb-2">Tổng điểm đạt được</div>
                    <div class="d-flex align-items-baseline gap-3 mb-4">
                        <div class="score-huge">{{ number_format($attempt->total_score, 2) }}</div>
                        <div class="fs-4 text-white text-opacity-50">/ 10</div>
                    </div>
                    <div class="progress bg-white bg-opacity-20 rounded-pill mb-4" style="height: 8px;">
                        <div class="progress-bar bg-white rounded-pill" role="progressbar" style="width: {{ $attempt->total_score * 10 }}%"></div>
                    </div>
                    <div class="row text-center bg-white bg-opacity-10 rounded-4 py-3 mx-0">
                        <div class="col-6 border-end border-white border-opacity-25">
                            <div class="small text-white text-opacity-75">Trung bình lớp</div>
                            <div class="fw-bold fs-5">{{ number_format($averageScore, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-white text-opacity-75">Cao nhất lớp</div>
                            <div class="fw-bold fs-5">{{ number_format($maxScore, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột Biểu Đồ (Dùng col-lg-auto để cột tự co lại theo nội dung) --}}
            <div class="col-lg-auto">
                {{-- Padding p-4 để tạo khoảng trắng vừa phải quanh biểu đồ --}}
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-bar-chart-fill text-primary me-2"></i>So sánh năng lực</h6>
                        <span class="badge bg-light text-secondary border ms-3">Thang điểm 10</span>
                    </div>
                    
                    {{-- Kích thước cố định cho khung chứa biểu đồ -> Card sẽ ôm theo kích thước này --}}
                    <div style="width: 420px; height: 240px;"> 
                        <canvas id="scoreChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- MIDDLE SECTION: CHI TIẾT BÀI LÀM --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <h4 class="fw-bold text-dark m-0">Chi tiết bài làm</h4>
            <div class="h-1px bg-secondary opacity-25 flex-grow-1"></div>
        </div>

        @php
            $userElective = null;
            foreach($groupedQuestions as $group) {
                $qOri = strtolower(trim($group->first()->question->orientation ?? ''));
                if ($qOri === 'cs' || $qOri === 'ict') {
                    $userElective = $qOri;
                    break;
                }
            }

            // SẮP XẾP: SAI LÊN ĐẦU
            $sortedQuestions = $groupedQuestions->sortBy(function($group) {
                return $group->contains('is_correct', false) ? 0 : 1;
            });
        @endphp

        @foreach($sortedQuestions as $groupId => $group)
            @php
                $firstItem = $group->first();
                $mainQuestion = $firstItem->question->parent ?? $firstItem->question;
                
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

                    {{-- GỢI Ý ÔN TẬP --}}
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

    {{-- CHART SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('scoreChart').getContext('2d');
            
            const gradUser = ctx.createLinearGradient(0, 0, 0, 400);
            gradUser.addColorStop(0, '#6366f1'); gradUser.addColorStop(1, '#4f46e5');

            const gradAvg = ctx.createLinearGradient(0, 0, 0, 400);
            gradAvg.addColorStop(0, '#cbd5e1'); gradAvg.addColorStop(1, '#94a3b8');

            const gradMax = ctx.createLinearGradient(0, 0, 0, 400);
            gradMax.addColorStop(0, '#34d399'); gradMax.addColorStop(1, '#10b981');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [''], 
                    datasets: [
                        {
                            label: 'Bạn',
                            data: [{{ $attempt->total_score }}],
                            backgroundColor: gradUser,
                            borderRadius: {topLeft: 8, topRight: 0, bottomLeft: 0, bottomRight: 0},
                            barPercentage: 1.0, 
                            categoryPercentage: 0.5 
                        },
                        {
                            label: 'TB Lớp',
                            data: [{{ $averageScore }}],
                            backgroundColor: gradAvg,
                            borderRadius: 0, 
                            barPercentage: 1.0,
                            categoryPercentage: 0.5
                        },
                        {
                            label: 'Cao nhất',
                            data: [{{ $maxScore }}],
                            backgroundColor: gradMax,
                            borderRadius: {topLeft: 0, topRight: 8, bottomLeft: 0, bottomRight: 0},
                            barPercentage: 1.0,
                            categoryPercentage: 0.5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 25, font: { family: "'Plus Jakarta Sans', sans-serif" } } },
                        tooltip: { backgroundColor: '#1e293b', padding: 12, cornerRadius: 8, displayColors: true }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, max: 10,
                            grid: { borderDash: [5, 5], color: '#f1f5f9' },
                            ticks: { font: { family: "'Plus Jakarta Sans', sans-serif" } }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
</x-app-layout>