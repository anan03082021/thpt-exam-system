<x-app-layout>
@php
    // 1. Tính toán thời gian kết thúc bài thi
    if (isset($session) && $session->id != 0) {
        $endTimeTimestamp = \Carbon\Carbon::parse($session->end_at)->timestamp;
    } else {
        $duration = $exam->duration ?? 45; 
        $endTimeTimestamp = now()->addMinutes($duration)->timestamp;
    }

    // 2. Lấy lựa chọn môn học từ session
    $savedElective = $savedElective ?? session('elective_choice_' . ($session->id ?? 0), '');

    // 3. Khởi tạo danh sách câu hỏi
    $allQuestions = collect($chungQuestions ?? []);
    if ($savedElective === 'cs') {
        $allQuestions = $allQuestions->concat($csQuestions ?? []);
    } elseif ($savedElective === 'ict') {
        $allQuestions = $allQuestions->concat($ictQuestions ?? []);
    }

    $totalQuestions = $allQuestions->count();
    $chungCount = count($chungQuestions ?? []);
    $totalSteps = ($savedElective === '') ? ($chungCount + 1) : $totalQuestions;
@endphp

    @push('styles')
    <style>
        :root { 
            --primary-color: #2d3275; 
            --accent-color: #f68b1f; 
            --bg-body: #f4f7fa; 
            --done-yellow: #ffc107;
            --radius: 12px;
        }

        /* 1. KHÓA LĂN CHUỘT TOÀN TRANG */
        body { 
            background-color: var(--bg-body); 
            padding-top: 110px; 
            padding-bottom: 65px; 
            overflow: hidden; 
            font-family: 'Inter', system-ui, sans-serif; 
            height: 100vh;
        }

        nav.navbar, footer { display: none !important; }
        [x-cloak] { display: none !important; }

        /* Tùy chỉnh nút điều hướng dưới cùng */
.btn-nav { 
    padding: 8px 20px; 
    border-radius: 50px; /* Bo tròn hoàn toàn dạng viên thuốc */
    font-weight: 600; 
    border: 1px solid var(--border-color); 
    background: #fff; 
    color: var(--primary-color); 
    transition: all 0.3s ease; 
    display: flex; 
    align-items: center; 
    gap: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.btn-nav:hover:not(:disabled) { 
    background: var(--primary-color); 
    color: #fff; 
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(45,50,117,0.2);
}

.btn-nav:disabled { 
    opacity: 0.4; 
    cursor: not-allowed; 
    background: #f8fafc;
}

/* Nút Câu tiếp theo nổi bật hơn */
.btn-next-action { 
    background: var(--accent-color); 
    color: #fff; 
    border-color: var(--accent-color); 
}

.btn-next-action:hover { 
    background: #e57d15; 
    border-color: #e57d15;
    color: #fff;
}
        
        /* 2. HEADER & NAV (FIXED) */
        .exam-header { 
            background: var(--primary-color); color: #fff; height: 55px; 
            position: fixed; top: 0; left: 0; right: 0; z-index: 1050; 
            display: flex; align-items: center; justify-content: space-between; padding: 0 25px; 
        }

        .question-nav-bar { 
            position: fixed; top: 55px; left: 0; right: 0; z-index: 1040; 
            height: 55px; background: #fff; border-bottom: 1px solid #eef2f7; 
            display: flex; align-items: center; padding: 0 15px; overflow-x: auto; gap: 10px;
        }

        .nav-item-q { 
            min-width: 38px; height: 38px; border-radius: 8px; 
            display: inline-flex; align-items: center; justify-content: center; 
            font-weight: 700; color: #64748b; border: 1px solid #e2e8f0; cursor: pointer; background: #fff;
        }
        .nav-item-q.active { background: var(--primary-color) !important; color: #fff !important; }
        .nav-item-q.done { background-color: var(--done-yellow) !important; color: #000; }

        /* 3. KHUNG CHỨA CÂU HỎI (QUAN TRỌNG) */
        .exam-content-wrapper {
            height: calc(100vh - 185px); /* Trừ header và footer điều hướng */
            max-width: 900px;
            margin: 0 auto;
            padding: 15px 20px;
            display: flex;
            flex-direction: column;
        }

        .question-main-card {
            background: #fff;
            border-radius: var(--radius);
            padding: 25px;
            border: 1px solid #eef2f7;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            max-height: 100%; /* Không vượt quá wrapper */
            height: auto;
        }

        /* VÙNG CUỘN NỘI BỘ CÂU HỎI */
        .q-scroll-area {
            overflow-y: auto;
            flex-grow: 1;
            padding-right: 8px;
        }
        .q-scroll-area::-webkit-scrollbar { width: 5px; }
        .q-scroll-area::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }

        .question-box { display: none; height: 100%; }
        .question-box.active { display: flex; flex-direction: column; }

        /* 4. STYLE CHI TIẾT CÂU HỎI */
        .q-badge { background: var(--primary-color); color: #fff; padding: 4px 12px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; }
        .q-content { font-size: 1.15rem; line-height: 1.6; color: #1e293b; font-weight: 600; margin-bottom: 20px; }
        
        .option-item { cursor: pointer; width: 100%; margin-bottom: 10px; }
        .option-box { padding: 12px 18px; border: 1.5px solid #e2e8f0; border-radius: 10px; transition: 0.2s; display: flex; align-items: center; }
        .option-item input:checked + .option-box { background-color: #f0f4ff; border-color: var(--primary-color); border-width: 2px; }
        
        .radio-dot { width: 18px; height: 18px; border: 2px solid #cbd5e1; border-radius: 50%; margin-right: 12px; position: relative; }
        .option-item input:checked + .option-box .radio-dot { background: var(--primary-color); border-color: var(--primary-color); }

        /* 5. BOTTOM BAR (FIXED) */
        .exam-bottom-bar { 
            position: fixed; bottom: 0; left: 0; right: 0; height: 60px; 
            background: #fff; border-top: 1px solid #eef2f7; 
            display: flex; align-items: center; justify-content: space-between; padding: 0 25px; z-index: 1050; 
        }
    </style>
    @endpush

<div x-data="{ 
    currentIdx: 0, 
    totalSteps: {{ $totalSteps }},
    totalQuestions: {{ $totalQuestions }},
    chungCount: {{ $chungCount }},
    elective: '{{ $savedElective }}',
    doneList: [],
    init() { this.updateDoneList(); },
    updateDoneList() {
        let done = [];
        document.querySelectorAll('input[type=radio]:checked').forEach(input => {
            let match = input.name.match(/\[(\d+)\]/);
            if(match) { let qId = match[1]; if(!done.includes(qId)) done.push(qId); }
        });
        this.doneList = done;
    },
    goTo(idx) {
        if(idx >= 0 && idx < this.totalSteps) {
            this.currentIdx = idx;
            this.$nextTick(() => {
                let navBtns = document.querySelectorAll('.nav-item-q');
                if(navBtns[idx]) navBtns[idx].scrollIntoView({ behavior: 'smooth', inline: 'center' });
            });
        }
    }
}" x-init="init()" x-cloak>

    {{-- HEADER --}}
<div class="exam-header shadow-sm">
    <div class="fw-bold text-uppercase d-flex align-items-center">
        <i class="bi bi-cpu-fill me-2"></i> {{ $exam->title }}
    </div>
    
    <div class="d-flex align-items-center gap-2 gap-md-3">
        {{-- Khu vực đồng hồ đếm ngược --}}
        <div class="countdown-wrapper d-flex align-items-center px-3 py-1 rounded-pill border" 
             style="background: #f0f2ff; border-color: #dbeafe !important; color: var(--primary-color);">
            <i class="bi bi-clock-history me-2 fw-bold"></i>
            <span id="countdown" class="fw-bold fs-5" style="font-variant-numeric: tabular-nums; min-width: 65px; text-align: center;">--:--</span>
        </div>

        <button type="button" class="btn btn-warning text-dark fw-bold px-3 px-md-4 shadow-sm rounded-pill" onclick="confirmSubmit()">
            NỘP BÀI
        </button>
    </div>
</div>

    {{-- THANH SỐ CÂU HỎI --}}
    <div class="question-nav-bar shadow-sm">
        @foreach($allQuestions as $idx => $q)
            <div class="nav-item-q" 
                 :class="{ 'active': currentIdx === {{ $idx }}, 'done': doneList.includes('{{ $q->id }}') }"
                 @click="goTo({{ $idx }})">
                {{ $idx + 1 }}
            </div>
        @endforeach
    </div>

    {{-- VÙNG NỘI DUNG CHÍNH --}}
    <div class="exam-content-wrapper">
        @foreach($allQuestions as $idx => $question)
            <div class="question-box" :class="{ 'active': currentIdx === {{ $idx }} }">
                <div class="question-main-card">
                    {{-- Tiêu đề câu hỏi --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-shrink-0">
                        <span class="q-badge">Câu {{ $idx + 1 }}</span>
                        <div class="text-primary fw-bold">1.0 điểm</div>
                    </div>
                    
                    {{-- Nội dung cuộn --}}
                    <div class="q-scroll-area">
                        <div class="q-content">{!! $question->content !!}</div>

                        <div class="q-options">
                            @if($question->type == 'single_choice')
                                @foreach($question->answers as $ans)
                                    <label class="option-item">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $ans->id }}" 
                                               class="d-none" onchange="markDoneUI('{{ $question->id }}')"
                                               {{ (isset($userAnswers[$question->id]) && $userAnswers[$question->id] == $ans->id) ? 'checked' : '' }}>
                                        <div class="option-box">
                                            <div class="radio-dot"></div>
                                            <span>{{ $ans->content }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            @elseif($question->type == 'true_false_group')
                                <div class="table-responsive rounded-3 border">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr class="text-center small fw-bold">
                                                <th class="text-start ps-3 py-2">Nội dung</th>
                                                <th width="60">Đúng</th>
                                                <th width="60">Sai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($question->children as $child)
                                                <tr>
                                                    <td class="ps-3 py-2 small">{!! $child->content !!}</td>
                                                    @foreach($child->answers as $ans)
                                                        @php $isTrue = str_contains(mb_strtolower($ans->content), 'đúng'); @endphp
                                                        <td class="text-center">
                                                            <input class="btn-check" type="radio" name="answers[{{ $child->id }}]" 
                                                                   id="tf-{{ $child->id }}-{{ $ans->id }}" value="{{ $ans->id }}"
                                                                   onchange="markDoneUI('{{ $question->id }}')"
                                                                   {{ (isset($userAnswers[$child->id]) && $userAnswers[$child->id] == $ans->id) ? 'checked' : '' }}>
                                                            <label class="btn btn-outline-{{ $isTrue ? 'success' : 'danger' }} btn-sm rounded-circle p-0" 
                                                                   for="tf-{{ $child->id }}-{{ $ans->id }}" style="width: 24px; height: 24px;">
                                                                <i class="bi bi-{{ $isTrue ? 'check' : 'x' }}"></i>
                                                            </label>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- TRANG CHỌN MÔN --}}
        @if($savedElective === '')
            <div class="question-box" :class="{ 'active': currentIdx === {{ $chungCount }} }">
                <div class="question-main-card text-center justify-content-center border-dashed border-primary">
                    <div class="mb-4 text-primary"><i class="bi bi-layers-half" style="font-size: 3rem;"></i></div>
                    <h3 class="fw-bold mb-3">LỰA CHỌN PHẦN THI TỰ CHỌN</h3>
                    <p class="text-muted mb-5">Bạn đã xong phần chung. Hãy chọn định hướng tiếp theo.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" @click="selectElective('cs')" class="btn btn-primary btn-lg px-4 fw-bold">KHOA HỌC MÁY TÍNH</button>
                        <button type="button" @click="selectElective('ict')" class="btn btn-success btn-lg px-4 fw-bold">TIN HỌC ỨNG DỤNG</button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ĐIỀU KHIỂN DƯỚI --}}
<div class="exam-bottom-bar shadow-lg">
    <button type="button" class="btn-nav" :disabled="currentIdx === 0" @click="goTo(currentIdx - 1)">
        <i class="bi bi-chevron-left"></i> <span>Câu trước</span>
    </button>

    <div class="progress-indicator d-flex align-items-center">
        <div class="me-2 d-none d-sm-block">Tiến độ:</div>
        <div class="fw-bold px-3 py-1 rounded-pill" style="background: var(--primary-color); color: #fff;">
            <span x-text="doneList.length"></span> / <span x-text="totalQuestions"></span>
        </div>
    </div>
    
    <div class="d-flex gap-2">
        <button type="button" class="btn-nav btn-next-action" x-show="currentIdx < totalSteps - 1" @click="goTo(currentIdx + 1)">
            <span>Câu tiếp theo</span> <i class="bi bi-chevron-right"></i>
        </button>

        <button type="button" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm" x-show="elective !== '' && currentIdx === totalQuestions - 1" onclick="confirmSubmit()">
            NỘP BÀI THI <i class="bi bi-send-check-fill ms-1"></i>
        </button>
    </div>
</div>
</div>

<form id="examForm" action="{{ route('exam.submit', $session->id) }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="exam_id_hidden" value="{{ $exam->id }}">
</form>

@push('scripts')
<script>
    function markDoneUI(qid) {
        const el = document.querySelector('[x-data]');
        if(el) {
            const data = Alpine.$data(el);
            if(!data.doneList.includes(String(qid))) { data.doneList.push(String(qid)); }
        }
    }

    function confirmSubmit() {
        if(confirm("Bạn có chắc chắn muốn nộp bài?")) {
            const form = document.getElementById('examForm');
            document.querySelectorAll('input[type=radio]:checked').forEach(radio => {
                let input = document.createElement('input');
                input.type = 'hidden'; input.name = radio.name; input.value = radio.value;
                form.appendChild(input);
            });
            form.submit();
        }
    }

    let end = {{ $endTimeTimestamp }} * 1000;
    setInterval(() => {
        let d = end - new Date().getTime();
        if(d < 0) { confirmSubmit(); }
        let m = Math.floor((d % 3600000) / 60000), s = Math.floor((d % 60000) / 1000);
        document.getElementById("countdown").innerText = (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
    }, 1000);

    function selectElective(t) {
        fetch("{{ route('exam.saveElective', $session->id) }}", {
            method:"POST", 
            headers:{"Content-Type":"application/json","X-CSRF-TOKEN":"{{csrf_token()}}"},
            body:JSON.stringify({elective:t})
        }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
    }
</script>
@endpush
</x-app-layout>