<x-app-layout>
    {{-- LOGIC TÍNH TOÁN THỜI GIAN --}}
    @php
        if ($session->id == 0) {
            $duration = $exam->duration ?? 45; 
            $endTimeTimestamp = now()->addMinutes($duration)->timestamp;
        } else {
            $endTimeTimestamp = \Carbon\Carbon::parse($session->end_at)->timestamp;
        }
        
        // Đếm số lượng câu chung để tính số thứ tự tiếp theo
        $chungCount = $chungQuestions->count();
        
        // Lấy giá trị từ Session nếu đã chọn trước đó
        $savedElective = session('elective_choice_' . $session->id, '');
    @endphp

    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4338ca;      
            --primary-soft: #e0e7ff;  
            --accent: #6366f1;        
            --bg-body: #f3f4f6;       
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
        
        /* 1. HEADER HIỆN ĐẠI */
        .exam-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            height: 72px; border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 30px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }
        .main-content { margin-top: 30px; }

        /* ĐỒNG HỒ SỐ */    
        .sidebar-timer-wrapper {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: white; padding: 15px; border-radius: 12px;
            text-align: center; margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }
        .timer-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; margin-bottom: 5px; }
        .timer-display { font-family: 'Inter', monospace; font-size: 2rem; font-weight: 800; line-height: 1; }

        /* 2. CARD CÂU HỎI */
        .question-card {
            background: var(--bg-card); border: 1px solid transparent;
            border-radius: 16px; padding: 30px; margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }
        .question-card:hover, .question-card:focus-within { 
            transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); border-color: var(--primary-soft);
        }
        .question-number {
            font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em;
            color: var(--primary); margin-bottom: 16px; font-weight: 800;
            background: var(--primary-soft); display: inline-block; padding: 4px 10px; border-radius: 6px;
        }
        .question-text { font-size: 1.15rem; color: var(--text-main); line-height: 1.6; margin-bottom: 24px; font-weight: 600; }

        /* 3. ĐÁP ÁN TRẮC NGHIỆM */
        .answer-grid { display: grid; gap: 14px; }
        .answer-option {
            display: flex; align-items: center; padding: 16px 20px;
            border: 1px solid var(--border-light); border-radius: 12px;
            cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fff; position: relative; overflow: hidden;
        }
        .answer-option:hover { border-color: var(--primary); background-color: #fafafa; }
        .answer-option.selected {
            border-color: var(--primary); background-color: var(--primary-soft);
            box-shadow: 0 0 0 1px var(--primary);
        }
        .option-marker {
            width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
            border-radius: 10px; background: #f3f4f6; color: var(--text-sub);
            margin-right: 18px; font-size: 0.95rem; flex-shrink: 0; font-weight: 700; transition: all 0.2s;
        }
        .answer-option.selected .option-marker {
            background: var(--primary); color: white; box-shadow: 0 2px 5px rgba(67, 56, 202, 0.3);
        }
        .answer-content { font-size: 1rem; color: var(--text-main); font-weight: 500; }
        .answer-option.selected .answer-content { color: var(--primary); font-weight: 600; }

        /* 4. CÂU HỎI ĐÚNG/SAI */
        .tf-container { border: 1px solid var(--border-light); border-radius: 12px; overflow: hidden; background: #fafafa; }
        .tf-row { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; background: #fff; border-bottom: 1px solid var(--border-light); }
        .tf-row:last-child { border-bottom: none; }
        .tf-content { flex: 1; margin-right: 24px; font-weight: 500; font-size: 1rem; color: #374151; }
        .btn-tf {
            padding: 8px 18px; border: 1px solid var(--border-light); border-radius: 8px;
            background: white; cursor: pointer; font-size: 0.9rem; font-weight: 600;
            color: var(--text-sub); transition: all 0.2s; display: flex; align-items: center; gap: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .btn-tf:hover { transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .btn-tf.selected-true { background-color: #ecfdf5; border-color: var(--success); color: var(--success); box-shadow: none; }
        .btn-tf.selected-false { background-color: #fef2f2; border-color: var(--danger); color: var(--danger); box-shadow: none; }

        /* 5. SIDEBAR */
        .sidebar-container {
            position: sticky; top: 100px; background: white; border-radius: 16px; border: none;
            padding: 24px; height: calc(100vh - 120px); overflow-y: auto;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }
        .grid-wrapper { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; }
        .grid-item {
            aspect-ratio: 1; border-radius: 10px; background: #fff; border: 1px solid var(--border-light);
            display: flex; align-items: center; justify-content: center; color: var(--text-sub);
            font-weight: 600; font-size: 0.9rem; text-decoration: none; transition: all 0.2s;
        }
        .grid-item:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-soft); }
        .grid-item.done { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 4px 6px rgba(67, 56, 202, 0.25); }

        /* BUTTON SUBMIT */
        .btn-submit-exam { background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); border: none; color: white; transition: opacity 0.2s; }
        .btn-submit-exam:hover { opacity: 0.9; color: white; }
        
        [x-cloak] { display: none !important; }
    </style>
    @endpush

    {{-- WRAP TOÀN BỘ TRONG ALPINE.JS ĐỂ QUẢN LÝ TRẠNG THÁI --}}
    <div x-data="{ elective: '{{ $savedElective }}' }" class="container main-content pb-5">
        
        <form id="examForm" action="{{ route('exam.submit', $session->id) }}" method="POST">
            @csrf
            <input type="hidden" name="exam_id_hidden" value="{{ $exam->id }}">
            
            {{-- INPUT HIDDEN LƯU GIÁ TRỊ CHỌN --}}
            <input type="hidden" name="selected_elective" x-model="elective">

            <div class="row g-4">
                {{-- CỘT TRÁI: DANH SÁCH CÂU HỎI --}}
                <div class="col-lg-9">
                    
                    {{-- 1. PHẦN CHUNG (LUÔN HIỂN THỊ) --}}
                    @if($chungQuestions->count() > 0)
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-3 ps-2 border-start border-4 border-primary">PHẦN I: KIẾN THỨC CHUNG</h5>
                            @foreach($chungQuestions as $index => $question)
                                @include('exam.partials.question_item', ['question' => $question, 'index' => $index + 1])
                            @endforeach
                        </div>
                    @endif

                    {{-- 2. KHUNG LỰA CHỌN PHÂN BAN (HIỆN KHI CHƯA CHỌN) --}}
                    <div class="question-card text-center p-5" x-show="!elective">
                        <h4 class="fw-bold mb-3 text-dark">PHẦN II: TỰ CHỌN ĐỊNH HƯỚNG</h4>
                        <p class="text-muted mb-4">Vui lòng chọn một trong hai định hướng để tiếp tục làm bài:</p>
                        
                        <div class="d-flex justify-content-center gap-4 flex-wrap">
                            <button type="button" @click="selectElective('cs')" class="btn btn-outline-primary py-3 px-5 fw-bold rounded-pill border-2">
                                <i class="bi bi-cpu fs-4 d-block mb-1"></i> Khoa học máy tính (CS)
                            </button>
                            <button type="button" @click="selectElective('ict')" class="btn btn-outline-success py-3 px-5 fw-bold rounded-pill border-2">
                                <i class="bi bi-laptop fs-4 d-block mb-1"></i> Tin học ứng dụng (ICT)
                            </button>
                        </div>
                    </div>

                    {{-- 3A. PHẦN KHOA HỌC MÁY TÍNH (CS) --}}
                    <template x-if="elective === 'cs'">
                        <div>
                            <div class="alert alert-primary fw-bold mb-4 shadow-sm border-0" style="background: #e0e7ff; color: #4338ca;">
                                <i class="bi bi-info-circle-fill me-2"></i> ĐANG LÀM BÀI: KHOA HỌC MÁY TÍNH (CS)
                            </div>
                            @foreach($csQuestions as $index => $question)
                                {{-- Số thứ tự nối tiếp phần chung --}}
                                @include('exam.partials.question_item', ['question' => $question, 'index' => $chungCount + $index + 1])
                            @endforeach
                        </div>
                    </template>

                    {{-- 3B. PHẦN TIN HỌC ỨNG DỤNG (ICT) --}}
                    <template x-if="elective === 'ict'">
                        <div>
                            <div class="alert alert-success fw-bold mb-4 shadow-sm border-0" style="background: #d1fae5; color: #065f46;">
                                <i class="bi bi-info-circle-fill me-2"></i> ĐANG LÀM BÀI: TIN HỌC ỨNG DỤNG (ICT)
                            </div>
                            @foreach($ictQuestions as $index => $question)
                                @include('exam.partials.question_item', ['question' => $question, 'index' => $chungCount + $index + 1])
                            @endforeach
                        </div>
                    </template>

                </div>

                {{-- CỘT PHẢI: SIDEBAR TIẾN ĐỘ --}}
                <div class="col-lg-3">
                    <div class="sidebar-container">
                        
                        {{-- ĐỒNG HỒ --}}
                        <div class="sidebar-timer-wrapper">
                            <div class="timer-label"><i class="bi bi-alarm me-1"></i> Thời gian còn lại</div>
                            <div id="countdown" class="timer-display">00:00:00</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold mb-0 text-dark">Tiến độ làm bài</h6>
                            <span class="badge bg-indigo-50 text-primary border border-primary-subtle rounded-pill px-3 py-2" id="progress-text">0/--</span>
                        </div>

                        {{-- GRID CÂU HỎI: Dùng x-show để ẩn/hiện ô số theo lựa chọn --}}
                        <div class="grid-wrapper">
                            @php $globalIndex = 1; @endphp

                            {{-- Grid Chung --}}
                            @foreach($chungQuestions as $question)
                                <a href="#question-{{ $question->id }}" id="grid-btn-{{ $question->id }}" class="grid-item">{{ $globalIndex++ }}</a>
                            @endforeach

                            {{-- Grid CS --}}
                            @foreach($csQuestions as $question)
                                <a href="#question-{{ $question->id }}" id="grid-btn-{{ $question->id }}" 
                                   class="grid-item" x-show="elective === 'cs'" x-cloak>{{ $globalIndex++ }}</a>
                            @endforeach

                            {{-- Grid ICT --}}
                            @foreach($ictQuestions as $question)
                                <a href="#question-{{ $question->id }}" id="grid-btn-{{ $question->id }}" 
                                   class="grid-item" x-show="elective === 'ict'" x-cloak>{{ $globalIndex++ }}</a>
                            @endforeach
                        </div>

                        <hr class="my-4 border-light">
                        
                        <div class="d-grid gap-3" x-show="elective || {{ $csQuestions->count() == 0 && $ictQuestions->count() == 0 ? 'true' : 'false' }}">
                            <button type="button" class="btn btn-submit-exam py-3 fw-bold rounded-3 shadow-sm" onclick="confirmSubmit()">
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
        // --- 1. XỬ LÝ CHỌN ĐÁP ÁN ĐƠN ---
        function selectAnswer(questionId, answerId) {
            let currentCard = document.getElementById('option-card-' + questionId + '-' + answerId);
            let parentGrid = currentCard.parentElement;
            parentGrid.querySelectorAll('.answer-option').forEach(el => el.classList.remove('selected'));
            currentCard.classList.add('selected');
            document.getElementById('radio-' + questionId + '-' + answerId).checked = true;
            markQuestionDone(questionId);
        }

        // --- [MỚI] HÀM XỬ LÝ CHỌN VÀ KHÓA ĐỊNH HƯỚNG ---
        function selectElective(type) {
            if (!confirm('Bạn có chắc chắn chọn phần ' + (type === 'cs' ? 'CS' : 'ICT') + '? Bạn sẽ KHÔNG thể thay đổi sau khi chọn.')) {
                return;
            }

            // Gọi AJAX lưu vào Session
            fetch("{{ route('exam.saveElective', $session->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ elective: type })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật giao diện bằng Alpine.js
                    document.querySelector('[x-data]')._x_dataStack[0].elective = type;
                    
                    // Cuộn xuống phần câu hỏi vừa hiện ra
                    setTimeout(() => {
                        let target = document.querySelector('.question-card');
                        if(target) {
                             window.scrollTo({ top: target.offsetHeight, behavior: 'smooth' });
                        }
                    }, 100);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            });
        }

        // --- 2. XỬ LÝ CHỌN ĐÚNG/SAI ---
        function selectTFAnswer(parentId, childId, answerId, type) {
            document.getElementById('radio-' + childId + '-' + answerId).checked = true;
            let clickedBtn = document.getElementById('tf-btn-' + childId + '-' + answerId);
            let parentRow = clickedBtn.parentElement;
            parentRow.querySelectorAll('.btn-tf').forEach(el => el.classList.remove('selected-true', 'selected-false'));
            if (type === 'true') clickedBtn.classList.add('selected-true');
            else clickedBtn.classList.add('selected-false');
            markQuestionDone(parentId);
        }

        // --- HÀM PHỤ ---
        function markQuestionDone(questionId) {
            let gridBtn = document.getElementById('grid-btn-' + questionId);
            if (!gridBtn.classList.contains('done')) {
                gridBtn.classList.add('done');
                updateProgress();
            }
        }

        function updateProgress() {
            let done = document.querySelectorAll('.grid-item.done').length;
            document.getElementById('progress-text').innerText = `${done} câu đã làm`;
        }

        // --- 3. ĐỒNG HỒ ---
        let endTime = {{ $endTimeTimestamp }} * 1000; 
        let timerInterval = setInterval(function() {
            let now = new Date().getTime();
            let distance = endTime - now;
            if (distance < 0) {
                clearInterval(timerInterval);
                document.getElementById("countdown").innerHTML = "00:00:00";
                if (!window.isSubmitted) {
                    window.isSubmitted = true;
                    alert("Đã hết giờ! Hệ thống đang tự động nộp bài...");
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
                    timerEl.style.animation = 'pulse 1s infinite';
                }
            }
        }, 1000);

        function confirmSubmit() {
            if (confirm("Bạn có chắc chắn muốn nộp bài?")) {
                window.isSubmitted = true;
                document.getElementById('examForm').submit();
            }
        }
    </script>
    @endpush
</x-app-layout>