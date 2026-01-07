<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    {{-- CSS RIÊNG CHO GIAO DIỆN NÀY --}}
    @push('styles')
    <style>
        /* Card câu hỏi */
        .card-correct { border-left: 5px solid #198754; background-color: #f8fff9; }
        .card-wrong { border-left: 5px solid #dc3545; background-color: #fff8f8; }

        /* Bảng True/False */
        .tf-result-table th { background-color: #f8f9fa; }
        .row-correct { background-color: rgba(25, 135, 84, 0.05); }
        .row-wrong { background-color: rgba(220, 53, 69, 0.05); }

        /* Trắc nghiệm đơn */
        .opt-item { padding: 10px 15px; border-radius: 8px; border: 1px solid #e9ecef; margin-bottom: 5px; background: white; }
        
        /* Đáp án người dùng chọn đúng */
        .opt-user-correct { padding: 10px 15px; border-radius: 8px; border: 1px solid #198754; background-color: #d1e7dd; color: #0f5132; margin-bottom: 5px; }
        
        /* Đáp án người dùng chọn sai */
        .opt-user-wrong { padding: 10px 15px; border-radius: 8px; border: 1px solid #dc3545; background-color: #f8d7da; color: #842029; margin-bottom: 5px; }
        
        /* Đáp án đúng (mà người dùng không chọn) */
        .opt-correct { padding: 10px 15px; border-radius: 8px; border: 1px solid #198754; background-color: #fff; color: #198754; margin-bottom: 5px; border-style: dashed; }
    </style>
    @endpush

    <div class="container py-4">
        {{-- HEADER & CHART GIỮ NGUYÊN NHƯ CŨ --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-primary mb-0">
                <i class="bi bi-trophy-fill me-2"></i>Kết quả Kỳ thi: {{ $attempt->examSession->title ?? 'Chi tiết' }}
            </h4>
            <a href="{{ route('student.history') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Quay lại lịch sử
            </a>
        </div>

        {{-- Phần biểu đồ so sánh (Giữ nguyên code cũ của bạn ở đây) --}}
        <div class="row g-4 mb-5">
            {{-- ... Code biểu đồ ... --}}
             <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="card-body text-center p-4">
                        <h6 class="text-uppercase fw-bold text-muted">Điểm của bạn</h6>
                        <h1 class="display-1 fw-bold text-primary mb-0">{{ number_format($attempt->total_score, 2) }}</h1>
                        <hr>
                         <div class="d-flex justify-content-between">
                            <span>Trung bình: <strong>{{ number_format($averageScore, 2) }}</strong></span>
                            <span>Cao nhất: <strong class="text-success">{{ number_format($maxScore, 2) }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body p-4">
                         <h6 class="fw-bold text-secondary">So sánh hiệu suất</h6>
                         <div style="height: 200px;"><canvas id="comparisonChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PHẦN CHI TIẾT BÀI LÀM (CODE CỦA BẠN ĐÃ ĐƯỢC CHÈN VÀO ĐÂY) --}}
        <div class="row justify-content-center">
            <div class="col-lg-10"> {{-- Tăng độ rộng lên chút cho đẹp --}}
                <h5 class="fw-bold text-primary mb-4 pb-2 border-bottom"><i class="bi bi-journal-check me-2"></i> Chi tiết bài làm</h5>

                @foreach($groupedQuestions as $groupId => $group)
                    @php
                        $firstItem = $group->first();
                        // Xác định câu hỏi chính (Nếu có parent thì lấy parent, không thì lấy chính nó)
                        $mainQuestion = $firstItem->question->parent ?? $firstItem->question;
                        
                        // Kiểm tra trong nhóm câu hỏi này có câu nào sai không
                        $groupHasWrong = $group->contains('is_correct', false);
                        
                        $cardClass = $groupHasWrong ? 'card-wrong' : 'card-correct';
                        $badgeClass = $groupHasWrong ? 'bg-danger' : 'bg-success';
                        
                        // Logic hiển thị text trạng thái
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
                            <div class="question-content fw-bold text-dark mb-3" style="font-size: 1.05rem;">
                                {!! $mainQuestion->content !!}
                            </div>

                            {{-- === TRƯỜNG HỢP 1: CÂU ĐÚNG/SAI CHÙM === --}}
                            @if($mainQuestion->type == 'true_false_group') 
                                <div class="table-responsive rounded-3 border">
                                    <table class="table tf-result-table mb-0">
                                        <thead class="table-light text-center small text-uppercase">
                                            <tr>
                                                <th class="text-start ps-3">Ý nhận định</th>
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
                                                    $icon = $ans->is_correct ? '<i class="bi bi-check-circle-fill text-success fs-5"></i>' : '<i class="bi bi-x-circle-fill text-danger fs-5"></i>';
                                                @endphp
                                                <tr class="{{ $rowClass }}">
                                                    <td class="text-start ps-3">{{ $ans->question->content }}</td>
                                                    <td class="text-center fw-bold {{ $ans->is_correct ? 'text-success' : 'text-danger' }}">{{ $userText }}</td>
                                                    <td class="text-center fw-bold text-primary">{{ $correctText }}</td>
                                                    <td class="text-center">{!! $icon !!}</td>
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

                                            // Logic xác định style cho từng dòng đáp án
                                            if ($opt->id == $userSelectedId) {
                                                if ($isThisOptCorrect) {
                                                    $rowStyle = 'opt-user-correct'; 
                                                    $icon = '<i class="bi bi-check-circle-fill me-2"></i>';
                                                } else {
                                                    $rowStyle = 'opt-user-wrong'; 
                                                    $icon = '<i class="bi bi-x-circle-fill me-2"></i>';
                                                }
                                            } elseif ($isThisOptCorrect) {
                                                $rowStyle = 'opt-correct'; 
                                                $icon = '<i class="bi bi-check-circle-fill me-2"></i>';
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

                            {{-- === GỢI Ý ÔN TẬP KHI LÀM SAI === --}}
                            @if($groupHasWrong)
                                <div class="hint-box mt-4 p-3 bg-warning bg-opacity-10 border border-warning rounded-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-lightbulb-fill text-warning me-3 fs-4"></i>
                                        <div class="w-100">
                                            <h6 class="fw-bold text-dark mb-2">Gợi ý ôn tập:</h6>
                                            <ul class="list-unstyled mb-0 small text-secondary">
                                                <li class="mb-2">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary me-2">Lớp {{ $mainQuestion->grade ?? '12' }}</span>
                                                    <span class="fw-bold text-dark">Chủ đề: {{ $mainQuestion->topic->name ?? 'Tổng hợp' }}</span>
                                                </li>
                                                @if($mainQuestion->coreContent)
                                                    <li class="mb-2 d-flex"><i class="bi bi-caret-right-fill text-secondary me-2"></i><div><span class="fw-bold">Nội dung cốt lõi:</span><br>{{ $mainQuestion->coreContent->name }}</div></li>
                                                @endif
                                                @if($mainQuestion->learningObjective)
                                                    <li class="d-flex"><i class="bi bi-check2-circle text-success me-2"></i><div><span class="fw-bold">Yêu cầu cần đạt:</span><br><span class="fst-italic text-dark">{{ $mainQuestion->learningObjective->content }}</span></div></li>
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
    </div>

    {{-- SCRIPT VẼ BIỂU ĐỒ (Giữ nguyên) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('comparisonChart').getContext('2d');
            const myScore = {{ $attempt->total_score ?? 0 }};
            const avgScore = {{ $averageScore ?? 0 }};
            const maxScore = {{ $maxScore ?? 0 }};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Điểm của bạn', 'Trung bình lớp', 'Cao nhất lớp'],
                    datasets: [{
                        label: 'Điểm số',
                        data: [myScore, avgScore, maxScore],
                        backgroundColor: ['rgba(13, 110, 253, 0.8)', 'rgba(108, 117, 125, 0.3)', 'rgba(25, 135, 84, 0.3)'],
                        borderColor: ['rgb(13, 110, 253)', 'rgb(108, 117, 125)', 'rgb(25, 135, 84)'],
                        borderWidth: 1, borderRadius: 8, barThickness: 60
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 10, grid: { borderDash: [5, 5] } }, x: { grid: { display: false } } } }
            });
        });
    </script>
</x-app-layout>