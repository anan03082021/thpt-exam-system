<x-app-layout>
    {{-- GIỮ NGUYÊN LOGIC TÍNH TOÁN THỜI GIAN --}}
    @php
        if ($session->id == 0) {
            $duration = $exam->duration ?? 45; 
            $endTimeTimestamp = now()->addMinutes($duration)->timestamp;
        } else {
            $endTimeTimestamp = \Carbon\Carbon::parse($session->end_at)->timestamp;
        }
    @endphp

    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4338ca;       /* Tím đậm sang trọng */
            --primary-soft: #e0e7ff;  /* Tím rất nhạt */
            --accent: #6366f1;        /* Tím sáng cho điểm nhấn */
            --bg-body: #f3f4f6;       /* Xám nền */
            --bg-card: #ffffff;
            --text-main: #111827;
            --text-sub: #6b7280;
            --border-light: #e5e7eb;
            --success: #059669;
            --danger: #dc2626;
        }

        body { 
            background-color: var(--bg-body); 
            font-family: 'Inter', sans-serif; 
            color: var(--text-main);
        }
        
        /* 1. HEADER HIỆN ĐẠI (GLASSMORPHISM) */
        .exam-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            height: 72px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }
        
        .main-content { margin-top: 30px; }

        /* ĐỒNG HỒ SỐ */    
        .sidebar-timer-wrapper {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: white;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }
        
        .timer-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .timer-display {
            font-family: 'Inter', monospace;
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(5, 150, 105, 0); }
            100% { box-shadow: 0 0 0 0 rgba(5, 150, 105, 0); }
        }

        /* 2. CARD CÂU HỎI (SẠCH & THOÁNG) */
        .question-card {
            background: var(--bg-card);
            border: 1px solid transparent;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }
        
        /* Hiệu ứng focus vào câu hỏi đang làm */
        .question-card:hover, .question-card:focus-within { 
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border-color: var(--primary-soft);
        }

        .question-number {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--primary);
            margin-bottom: 16px;
            font-weight: 800;
            background: var(--primary-soft);
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .question-text {
            font-size: 1.15rem;
            color: var(--text-main);
            line-height: 1.6;
            margin-bottom: 24px;
            font-weight: 600;
        }

        /* 3. ĐÁP ÁN TRẮC NGHIỆM (INTERACTIVE CARD) */
        .answer-grid { display: grid; gap: 14px; }

        .answer-option {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border: 1px solid var(--border-light);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        .answer-option:hover {
            border-color: var(--primary);
            background-color: #fafafa;
        }

        .answer-option.selected {
            border-color: var(--primary);
            background-color: var(--primary-soft);
            box-shadow: 0 0 0 1px var(--primary); /* Viền kép tạo điểm nhấn */
        }

        /* Marker A, B, C, D */
        .option-marker {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 10px;
            background: #f3f4f6;
            color: var(--text-sub);
            margin-right: 18px;
            font-size: 0.95rem;
            flex-shrink: 0;
            font-weight: 700;
            transition: all 0.2s;
        }

        .answer-option.selected .option-marker {
            background: var(--primary);
            color: white;
            box-shadow: 0 2px 5px rgba(67, 56, 202, 0.3);
        }
        
        .answer-content { font-size: 1rem; color: var(--text-main); font-weight: 500; }
        .answer-option.selected .answer-content { color: var(--primary); font-weight: 600; }

        /* 4. CÂU HỎI ĐÚNG/SAI (MODERN ROWS) */
        .tf-container {
            border: 1px solid var(--border-light);
            border-radius: 12px;
            overflow: hidden;
            background: #fafafa;
        }
        .tf-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            background: #fff;
            border-bottom: 1px solid var(--border-light);
        }
        .tf-row:last-child { border-bottom: none; }
        
        .tf-content { flex: 1; margin-right: 24px; font-weight: 500; font-size: 1rem; color: #374151; }
        
        .btn-tf {
            padding: 8px 18px;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            background: white;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-sub);
            transition: all 0.2s;
            display: flex; align-items: center; gap: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .btn-tf:hover { transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        .btn-tf.selected-true {
            background-color: #ecfdf5; border-color: var(--success); color: var(--success);
            box-shadow: none;
        }
        .btn-tf.selected-false {
            background-color: #fef2f2; border-color: var(--danger); color: var(--danger);
            box-shadow: none;
        }

        /* 5. SIDEBAR (FLOATING PANEL) */
        .sidebar-container {
            position: sticky;
            top: 100px;
            background: white;
            border-radius: 16px;
            border: none;
            padding: 24px;
            height: calc(100vh - 120px);
            overflow-y: auto;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
        }

        /* Scrollbar đẹp cho sidebar */
        .sidebar-container::-webkit-scrollbar { width: 6px; }
        .sidebar-container::-webkit-scrollbar-track { background: transparent; }
        .sidebar-container::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 20px; }

        .grid-wrapper {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }

        .grid-item {
            aspect-ratio: 1;
            border-radius: 10px;
            background: #fff;
            border: 1px solid var(--border-light);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-sub);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .grid-item:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-soft);
        }

        .grid-item.done {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 6px rgba(67, 56, 202, 0.25);
        }

        /* Nút Nộp bài Gradient */
        .btn-submit-exam {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border: none;
            color: white;
            transition: opacity 0.2s;
        }
        .btn-submit-exam:hover { opacity: 0.9; color: white; }

    </style>
    @endpush

    {{-- HEADER --}}


    {{-- NỘI DUNG CHÍNH --}}
    <div class="container main-content pb-5">
        <form id="examForm" action="{{ route('exam.submit', $session->id) }}" method="POST">
            @csrf
            <input type="hidden" name="exam_id_hidden" value="{{ $exam->id }}">

            <div class="row g-4">
                {{-- CỘT TRÁI: DANH SÁCH CÂU HỎI --}}
                <div class="col-lg-9">
                    @foreach($exam->questions as $index => $question)
                        <div class="question-card" id="question-{{ $question->id }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="question-number">Câu {{ $index + 1 }}</div>
                                @if($question->type == 'true_false_group')
                                    <span class="badge bg-light text-secondary border">Đúng / Sai</span>
                                @endif
                            </div>
                            
                            {{-- Nội dung câu hỏi --}}
                            <div class="question-text">
                                {!! $question->content !!}
                            </div>

                            @if($question->image)
                                <div class="mb-4 text-center">
                                    <img src="{{ asset('storage/' . $question->image) }}" class="img-fluid rounded-3 border" style="max-height: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                                </div>
                            @endif

                            {{-- LOẠI 1: CÂU HỎI CHÙM ĐÚNG/SAI --}}
                            @if($question->type == 'true_false_group')
                                <div class="tf-container">
                                    @foreach($question->children as $childIndex => $child)
                                        <div class="tf-row">
                                            <div class="tf-content">
                                                <span class="fw-bold me-2 text-primary">{{ $childIndex + 1 }}.</span>
                                                {!! $child->content !!}
                                            </div>
                                            
                                            <div class="tf-options">
                                                @foreach($child->answers as $ans)
                                                    @php 
                                                        $isTrue = stripos($ans->content, 'Đúng') !== false || stripos($ans->content, 'True') !== false;
                                                        $btnType = $isTrue ? 'true' : 'false';
                                                        $label = $isTrue ? 'Đúng' : 'Sai';
                                                        $icon = $isTrue ? 'bi-check-lg' : 'bi-x-lg';
                                                    @endphp

                                                    <div class="btn-tf" 
                                                         id="tf-btn-{{ $child->id }}-{{ $ans->id }}"
                                                         onclick="selectTFAnswer({{ $question->id }}, {{ $child->id }}, {{ $ans->id }}, '{{ $btnType }}')">
                                                        <i class="bi {{ $icon }}"></i> {{ $label }}
                                                    </div>

                                                    <input type="radio" 
                                                           name="answers[{{ $child->id }}]" 
                                                           value="{{ $ans->id }}" 
                                                           id="radio-{{ $child->id }}-{{ $ans->id }}" 
                                                           class="d-none">
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            {{-- LOẠI 2: TRẮC NGHIỆM ĐƠN --}}
                            @else
                                <div class="answer-grid">
                                    @foreach($question->answers as $ansKey => $answer)
                                        @php $char = ['A','B','C','D'][$ansKey] ?? '?'; @endphp
                                        
                                        <div class="answer-option" 
                                             id="option-card-{{ $question->id }}-{{ $answer->id }}"
                                             onclick="selectAnswer({{ $question->id }}, {{ $answer->id }})">
                                            
                                            <div class="option-marker">{{ $char }}</div>
                                            <div class="answer-content">{{ $answer->content }}</div>
                                        </div>

                                        <input type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="{{ $answer->id }}" 
                                               id="radio-{{ $question->id }}-{{ $answer->id }}" 
                                               class="d-none">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- CỘT PHẢI: SIDEBAR TIẾN ĐỘ --}}
{{-- CỘT PHẢI: SIDEBAR TIẾN ĐỘ --}}
                <div class="col-lg-3">
                    <div class="sidebar-container">
                        
                        {{-- [MỚI] ĐỒNG HỒ ĐẾM NGƯỢC ĐẶT TẠI ĐÂY --}}
                        <div class="sidebar-timer-wrapper">
                            <div class="timer-label"><i class="bi bi-alarm me-1"></i> Thời gian còn lại</div>
                            <div id="countdown" class="timer-display">00:00:00</div>
                        </div>
                        {{-- KẾT THÚC PHẦN ĐỒNG HỒ --}}

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold mb-0 text-dark">Tiến độ làm bài</h6>
                            <span class="badge bg-indigo-50 text-primary border border-primary-subtle rounded-pill px-3 py-2" id="progress-text">0/{{ $exam->questions->count() }}</span>
                        </div>

                        <div class="grid-wrapper">
                            @foreach($exam->questions as $index => $question)
                                <a href="#question-{{ $question->id }}" 
                                   id="grid-btn-{{ $question->id }}"
                                   class="grid-item">
                                    {{ $index + 1 }}
                                </a>
                            @endforeach
                        </div>

                        <hr class="my-4 border-light">
                        
                        <div class="d-grid gap-3">
                            <button type="button" class="btn btn-submit-exam py-3 fw-bold rounded-3 shadow-sm" 
                                    onclick="confirmSubmit()">
                                NỘP BÀI THI <i class="bi bi-send-fill ms-2"></i>
                            </button>
                            
                            <a href="{{ route('dashboard') }}" class="btn btn-white border text-muted fw-bold rounded-3">
                                <i class="bi bi-arrow-left me-1"></i> Thoát
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // --- 1. XỬ LÝ CHỌN ĐÁP ÁN ĐƠN (Single Choice) ---
        function selectAnswer(questionId, answerId) {
            let currentCard = document.getElementById('option-card-' + questionId + '-' + answerId);
            let parentGrid = currentCard.parentElement;
            parentGrid.querySelectorAll('.answer-option').forEach(el => el.classList.remove('selected'));
            
            currentCard.classList.add('selected');
            document.getElementById('radio-' + questionId + '-' + answerId).checked = true;

            markQuestionDone(questionId);
        }

        // --- 2. XỬ LÝ CHỌN ĐÁP ÁN ĐÚNG/SAI (True/False) ---
        function selectTFAnswer(parentId, childId, answerId, type) {
            document.getElementById('radio-' + childId + '-' + answerId).checked = true;

            let clickedBtn = document.getElementById('tf-btn-' + childId + '-' + answerId);
            let parentRow = clickedBtn.parentElement;
            parentRow.querySelectorAll('.btn-tf').forEach(el => {
                el.classList.remove('selected-true', 'selected-false');
            });

            if (type === 'true') {
                clickedBtn.classList.add('selected-true');
            } else {
                clickedBtn.classList.add('selected-false');
            }

            markQuestionDone(parentId);
        }

        // --- HÀM PHỤ: CẬP NHẬT SIDEBAR ---
        function markQuestionDone(questionId) {
            let gridBtn = document.getElementById('grid-btn-' + questionId);
            if (!gridBtn.classList.contains('done')) {
                gridBtn.classList.add('done');
                updateProgress();
            }
        }

        function updateProgress() {
            let total = {{ $exam->questions->count() }};
            let done = document.querySelectorAll('.grid-item.done').length;
            document.getElementById('progress-text').innerText = `${done}/${total}`;
        }

        // --- 3. ĐỒNG HỒ ĐẾM NGƯỢC ---
        let endTime = {{ $endTimeTimestamp }} * 1000; 

        let timerInterval = setInterval(function() {
            let now = new Date().getTime();
            let distance = endTime - now;

            if (distance < 0) {
                clearInterval(timerInterval);
                document.getElementById("countdown").innerHTML = "00:00:00";
                if (!window.isSubmitted) {
                    window.isSubmitted = true;
                    alert("Đã hết giờ làm bài! Hệ thống đang tự động nộp bài...");
                    document.getElementById('examForm').submit();
                }
                return;
            }

            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            hours = hours < 10 ? "0" + hours : hours;
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            let timerEl = document.getElementById("countdown");
            if(timerEl) {
                timerEl.innerHTML = hours + ":" + minutes + ":" + seconds;
                
                if (distance < 300000) { 
                    timerEl.style.color = '#dc2626';
                    timerEl.style.borderColor = '#dc2626';
                    // Hiệu ứng nhấp nháy cho đồng hồ khi sắp hết giờ
                    timerEl.style.animation = 'pulse 1s infinite';
                }
            }
        }, 1000);

        // --- 4. XÁC NHẬN NỘP BÀI ---
        function confirmSubmit() {
            let total = {{ $exam->questions->count() }};
            let done = document.querySelectorAll('.grid-item.done').length;
            
            let msg = "Bạn có chắc chắn muốn nộp bài?";
            if (done < total) {
                msg = `Bạn còn ${total - done} câu chưa làm. Bạn vẫn muốn nộp bài?`;
            }
            
            if (confirm(msg)) {
                window.isSubmitted = true;
                document.getElementById('examForm').submit();
            }
        }
    </script>
    @endpush
</x-app-layout>