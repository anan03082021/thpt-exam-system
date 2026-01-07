<x-app-layout>
    
    @push('styles')
    <style>
        /* CSS Điểm số */
        .score-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
        .score-circle { width: 150px; height: 150px; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; color: white; box-shadow: 0 8px 20px rgba(0,0,0,0.15); border: 5px solid rgba(255,255,255,0.4); }
        .bg-score-high { background: linear-gradient(135deg, #198754 0%, #20c997 100%); }
        .bg-score-mid { background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%); }
        .bg-score-low { background: linear-gradient(135deg, #dc3545 0%, #f06548 100%); }

        /* CSS Câu hỏi */
        .question-content img { max-width: 100%; height: auto; border-radius: 8px; border: 1px solid #dee2e6; margin: 10px 0; }
        .card-wrong { border-left: 5px solid #dc3545; background-color: #fff5f5; }
        .card-correct { border-left: 5px solid #198754; background-color: #f8fffa; }
        
        /* Table Đúng/Sai */
        .tf-result-table th, .tf-result-table td { vertical-align: middle; }
        .row-wrong { background-color: #ffe6e6 !important; }
        .row-correct { background-color: #d1e7dd !important; }
        
        /* Đáp án trắc nghiệm */
        .opt-item { border: 1px solid #e9ecef; border-radius: 8px; padding: 12px; margin-bottom: 8px; }
        .opt-user-wrong { background-color: #ffe6e6; border-color: #dc3545; color: #b02a37; }
        .opt-correct { background-color: #d1e7dd; border-color: #198754; color: #0f5132; }
        .opt-user-correct { background-color: #d1e7dd; border-color: #198754; color: #0f5132; font-weight: bold; border-width: 2px; }

        /* Gợi ý */
        .hint-box { background-color: #fff3cd; border: 1px solid #ffecb5; color: #856404; border-radius: 8px; padding: 10px 15px; margin-top: 15px; font-size: 0.9rem; }
    </style>
    @endpush

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @php
        $scoreValue = isset($score) && $score !== '' ? (float)$score : 0;
        if($scoreValue >= 8) { $bgClass = 'bg-score-high'; $msg = 'Xuất sắc!'; }
        elseif($scoreValue >= 5) { $bgClass = 'bg-score-mid'; $msg = 'Khá tốt!'; }
        else { $bgClass = 'bg-score-low'; $msg = 'Cần cố gắng'; }

        // Eager load thêm coreContent và learningObjective để hiển thị gợi ý
        $rawAnswers = $attemptDetail->attemptAnswers->load(['question.parent', 'question.answers', 'question.topic', 'question.coreContent', 'question.learningObjective']);

        $groupedQuestions = $rawAnswers->groupBy(function($ans) {
            return $ans->question->parent_id ?? $ans->question_id;
        });

        $groupedQuestions = $groupedQuestions->sortBy(function($group) {
            return $group->contains('is_correct', false) ? 0 : 1;
        });

        $totalWrongGroups = $groupedQuestions->filter(fn($g) => $g->contains('is_correct', false))->count();
    @endphp

    <div class="row g-4">
        {{-- CỘT TRÁI: ĐIỂM SỐ --}}
        <div class="col-lg-4">
            <div class="score-card mb-4 pb-4 text-center">
                <div class="pt-5 pb-3">
                    <div class="score-circle {{ $bgClass }}">
                        <span class="display-4 fw-bold">{{ number_format($scoreValue, 2) }}</span>
                        <span class="small opacity-75">điểm</span>
                    </div>
                </div>
                <h3 class="fw-bold {{ $scoreValue < 5 ? 'text-danger' : 'text-success' }}">{{ $msg }}</h3>
                
                <div class="row g-2 px-4 mt-3 mb-4">
                    <div class="col-6">
                        <div class="p-2 bg-danger bg-opacity-10 rounded text-danger fw-bold">
                            {{ $totalWrongGroups }} câu/nhóm sai
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-success bg-opacity-10 rounded text-success fw-bold">
                            {{ $groupedQuestions->count() - $totalWrongGroups }} câu/nhóm đúng
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 px-4">
                    <a href="{{ route('exam.take', $attemptDetail->exam_session_id ?? 0) }}" class="btn btn-primary fw-bold shadow-sm">
                        <i class="bi bi-arrow-repeat"></i> Làm lại bài
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Về trang chủ</a>
                </div>
            </div>

            {{-- Biểu đồ --}}
            @if(isset($chartData) && count($chartData) > 1)
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-secondary mb-3">Tiến bộ học tập</h6>
                        <canvas id="progressChart" height="200"></canvas>
                    </div>
                </div>
            @endif
        </div>

        {{-- CỘT PHẢI: CHI TIẾT BÀI LÀM --}}
        <div class="col-lg-8">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-journal-check me-2"></i> Chi tiết bài làm</h5>

            @foreach($groupedQuestions as $groupId => $group)
                @php
                    $firstItem = $group->first();
                    $mainQuestion = $firstItem->question->parent ?? $firstItem->question;
                    $groupHasWrong = $group->contains('is_correct', false);
                    
                    $cardClass = $groupHasWrong ? 'card-wrong' : 'card-correct';
                    $badgeClass = $groupHasWrong ? 'bg-danger' : 'bg-success';
                    $statusText = $groupHasWrong ? 'Có ý sai' : 'Đúng hoàn toàn';
                    if ($mainQuestion->type == 'single_choice') {
                        $statusText = $groupHasWrong ? 'Sai' : 'Đúng';
                    }
                @endphp

                <div class="card shadow-sm mb-4 {{ $cardClass }}">
                    <div class="card-body p-4">
                        
                        {{-- Header câu hỏi --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge {{ $badgeClass }} mb-2">{{ $statusText }}</span>
                                <span class="badge bg-light text-dark border">{{ $mainQuestion->topic->name ?? 'Tổng hợp' }}</span>
                            </div>
                            <small class="text-muted">ID: #{{ $mainQuestion->id }}</small>
                        </div>

                        {{-- Nội dung câu hỏi --}}
                        <div class="question-content fw-bold text-dark mb-3">
                            {!! $mainQuestion->content !!}
                        </div>

                        {{-- === TRƯỜNG HỢP 1: CÂU ĐÚNG/SAI CHÙM === --}}
                        @if($mainQuestion->type == 'true_false_group') 
                            <div class="table-responsive">
                                <table class="table table-bordered tf-result-table mb-0">
                                    <thead class="table-light text-center small text-uppercase">
                                        <tr>
                                            <th>Ý nhận định</th>
                                            <th width="15%">Bạn chọn</th>
                                            <th width="15%">Đáp án đúng</th>
                                            <th width="10%">Kết quả</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group as $ans)
                                            @php
                                                $correctOpt = $ans->question->answers->where('is_correct', true)->first();
                                                $correctText = $correctOpt ? $correctOpt->content : 'N/A';
                                                $userText = $ans->selectedAnswer->content ?? 'Bỏ trống';
                                                $rowClass = $ans->is_correct ? 'row-correct' : 'row-wrong';
                                                $icon = $ans->is_correct ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>';
                                            @endphp
                                            <tr class="{{ $rowClass }}">
                                                <td>{{ $ans->question->content }}</td>
                                                <td class="text-center fw-bold {{ $ans->is_correct ? 'text-success' : 'text-danger' }}">{{ $userText }}</td>
                                                <td class="text-center fw-bold text-primary">{{ $correctText }}</td>
                                                <td class="text-center fs-5">{!! $icon !!}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        {{-- === TRƯỜNG HỢP 2: TRẮC NGHIỆM ĐƠN === --}}
                        @elseif($mainQuestion->type == 'single_choice')
                            <div class="d-flex flex-column gap-2">
                                @php
                                    $userAnsItem = $group->first(); 
                                    $userSelectedId = $userAnsItem->selected_answer_id;
                                @endphp

                                @foreach($mainQuestion->answers as $opt)
                                    @php
                                        $isThisOptCorrect = $opt->is_correct;
                                        $rowStyle = 'opt-item';
                                        $icon = '<i class="bi bi-circle me-2 text-muted"></i>';

                                        if ($opt->id == $userSelectedId) {
                                            if ($isThisOptCorrect) {
                                                $rowStyle = 'opt-user-correct'; $icon = '<i class="bi bi-check-circle-fill me-2"></i>';
                                            } else {
                                                $rowStyle = 'opt-user-wrong'; $icon = '<i class="bi bi-x-circle-fill me-2"></i>';
                                            }
                                        } elseif ($isThisOptCorrect) {
                                            $rowStyle = 'opt-correct'; $icon = '<i class="bi bi-check-circle-fill me-2"></i>';
                                        }
                                    @endphp
                                    <div class="d-flex align-items-center {{ $rowStyle }}">
                                        {!! $icon !!} <span>{{ $opt->content }}</span>
                                        @if($isThisOptCorrect && $opt->id != $userSelectedId) <span class="ms-auto badge bg-success bg-opacity-75">Đáp án đúng</span> @endif
                                        @if($opt->id == $userSelectedId) <span class="ms-auto badge {{ $isThisOptCorrect ? 'bg-success' : 'bg-danger' }}">Bạn chọn</span> @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- [MỚI] HIỂN THỊ GỢI Ý NẾU LÀM SAI --}}
{{-- [MỚI] HIỂN THỊ GỢI Ý CHI TIẾT NẾU LÀM SAI --}}
                        @if($groupHasWrong)
                            <div class="hint-box mt-3 p-3 bg-warning bg-opacity-10 border border-warning rounded-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-lightbulb-fill text-warning me-2 fs-5 mt-1"></i>
                                    <div class="w-100">
                                        <h6 class="fw-bold text-dark mb-2">Gợi ý ôn tập:</h6>
                                        
                                        <ul class="list-unstyled mb-0 small text-secondary">
                                            {{-- 1. Lớp & Chủ đề --}}
                                            <li class="mb-2">
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary me-2">
                                                    Lớp {{ $mainQuestion->grade }}
                                                </span>
                                                <span class="fw-bold text-dark">
                                                    Chủ đề: {{ $mainQuestion->topic->name ?? 'Tổng hợp' }}
                                                </span>
                                            </li>

                                            {{-- 2. Nội dung cốt lõi (Nếu có) --}}
                                            @if($mainQuestion->coreContent)
                                                <li class="mb-2 d-flex">
                                                    <i class="bi bi-caret-right-fill text-secondary me-2"></i>
                                                    <div>
                                                        <span class="fw-bold">Nội dung cốt lõi:</span><br>
                                                        {{ $mainQuestion->coreContent->name }}
                                                    </div>
                                                </li>
                                            @endif
                                            
                                            {{-- 3. Yêu cầu cần đạt (Nếu có) --}}
                                            @if($mainQuestion->learningObjective)
                                                <li class="d-flex">
                                                    <i class="bi bi-check2-circle text-success me-2"></i>
                                                    <div>
                                                        <span class="fw-bold">Yêu cầu cần đạt:</span><br>
                                                        <span class="fst-italic text-dark">
                                                            {{ $mainQuestion->learningObjective->content }}
                                                        </span>
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif 

                    </div>
                </div>
            @endforeach

        </div>
    </div>

    {{-- SCRIPT BIỂU ĐỒ --}}
    @if(isset($chartData) && count($chartData) > 1)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('progressChart').getContext('2d');
            const data = @json($chartData);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [{
                        label: 'Điểm số',
                        data: data.map(d => d.score),
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: data.map(d => d.is_current ? '#dc3545' : '#fff'),
                        pointBorderColor: data.map(d => d.is_current ? '#dc3545' : '#0d6efd')
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, max: 10 } }
                }
            });
        });
    </script>
    @endif

</x-app-layout>