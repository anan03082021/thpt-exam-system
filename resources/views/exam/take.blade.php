<x-app-layout>
@php
    // Tính toán thời gian
    if (isset($session) && $session->id != 0) {
        $endTimeTimestamp = \Carbon\Carbon::parse($session->end_at)->timestamp;
    } else {
        $duration = $exam->duration ?? 45; 
        $endTimeTimestamp = now()->addMinutes($duration)->timestamp;
    }

    $savedElective = $savedElective ?? session('elective_choice_' . ($session->id ?? 0), '');
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
    /* 1. GHI ĐÈ BỐ CỤC APP.BLADE ĐỂ FULL MÀN HÌNH CHUẨN XÁC */
    html, body { 
        overflow: hidden !important; 
        height: 100vh !important; 
        width: 100vw !important;
        margin: 0; padding: 0;
    }
    main.container { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
    nav.navbar { display: none !important; }

    :root { 
        --primary-color: #2d3275; 
        --accent-color: #f68b1f; 
        --bg-body: #f4f6f9; 
        --done-yellow: #ffc107;
        --radius: 16px;
    }

    .exam-wrapper-fullscreen {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background-color: var(--bg-body); z-index: 1000;
        display: flex; flex-direction: column; height: 100vh;
    }

    [x-cloak] { display: none !important; }

    /* 2. HEADER & NAV */
    .exam-header { 
        background: #ffffff; color: var(--primary-color); height: 60px; 
        display: flex; align-items: center; justify-content: space-between; padding: 0 30px; 
        border-bottom: 1px solid #e2e8f0; box-shadow: 0 2px 5px rgba(0,0,0,0.02); flex-shrink: 0;
    }
    .countdown-pill {
        background: #f0f2ff; border: 1px solid #dbeafe; color: var(--primary-color);
        padding: 5px 18px; border-radius: 50px; font-weight: 800; font-size: 1.1rem;
        display: flex; align-items: center; gap: 8px;
    }

    .question-nav-bar { 
        height: 52px; background: #fff; border-bottom: 1px solid #eef2f7; 
        display: flex; align-items: center; padding: 0 20px; overflow-x: auto; gap: 8px; flex-shrink: 0;
    }
    .nav-item-q { 
        min-width: 36px; height: 36px; border-radius: 8px; 
        display: inline-flex; align-items: center; justify-content: center; 
        font-weight: 700; color: #64748b; border: 1.5px solid #e2e8f0; 
        cursor: pointer; background: #fff; transition: all 0.2s; font-size: 0.95rem; flex-shrink: 0;
    }
    .nav-item-q.active { background: var(--primary-color) !important; color: #fff !important; border-color: var(--primary-color); transform: scale(1.05); }
    .nav-item-q.done { background-color: var(--done-yellow) !important; color: #000; border-color: #e0a800; }

    /* 3. VÙNG NỘI DUNG CHÍNH (SỬA LỖI FLEXBOX VÀ CUỘN) */
    .exam-main-area {
        flex: 1; /* Thay flex-grow: 1 bằng flex: 1 để tương thích tốt hơn */
        display: flex; 
        justify-content: center; 
        align-items: center; /* Đảm bảo căn giữa */
        padding: 20px; 
        overflow: hidden; /* Cực kỳ quan trọng để thẻ con không bị tràn ra ngoài */
        min-height: 0; /* Bắt buộc để flexbox hoạt động đúng khi nội dung dài */
    }
    
    .question-box-container {
        width: 100%; 
        max-width: 950px; 
        display: flex; 
        flex-direction: column; 
        justify-content: center;
        /* Đảm bảo chiều cao không vượt quá khung nhìn */
        max-height: 100%; 
    }
    
    .question-main-card {
        background: #fff; border-radius: var(--radius); padding: 25px; 
        border: 1px solid #eef2f7; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        display: flex; flex-direction: column; 
        width: 100%; 
        /* KHÓA CHIỀU CAO Ở ĐÂY ĐỂ TRÁNH SQUISHED VÀ XUẤT HIỆN THANH CUỘN BÊN TRONG */
        max-height: 100%; 
        overflow: hidden; 
    }

    /* Vùng cuộn bên trong Card */
    .q-scroll-area { 
        overflow-y: auto; padding-right: 8px; 
        flex-shrink: 1; /* Chiếm toàn bộ không gian còn lại */
        min-height: 0; /* Bắt buộc để thanh cuộn hoạt động */
    }
    .q-scroll-area::-webkit-scrollbar { width: 6px; }
    .q-scroll-area::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    /* STYLE ĐÁP ÁN */
    .option-item { cursor: pointer; width: 100%; margin-bottom: 10px; display: block;}
    .option-box { 
        padding: 12px 18px; border: 1px solid #e2e8f0; border-radius: 10px; 
        transition: all 0.2s ease; display: flex; align-items: center; background: #fff;
    }
    .option-item:hover .option-box { border-color: var(--primary-color); background: #f8faff; }
    .option-item input:checked + .option-box { background-color: #f0f4ff; border-color: var(--primary-color); border-width: 2px; }
    
    .radio-dot { width: 18px; height: 18px; border: 2px solid #cbd5e1; border-radius: 50%; margin-right: 12px; position: relative; flex-shrink: 0;}
    .option-item input:checked + .option-box .radio-dot { border-color: var(--primary-color); }
    .option-item input:checked + .option-box .radio-dot::after {
        content: ""; width: 8px; height: 8px; background: var(--primary-color); 
        border-radius: 50%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    }

    .tf-table th { background: #f8fafc; color: #64748b; font-size: 0.85rem; border-bottom: 1px solid #e2e8f0; padding: 10px !important;}
    .tf-table td { padding: 12px 15px !important; }

    /* 4. FOOTER */
    .exam-bottom-bar { 
        height: 65px; background: #fff; border-top: 1px solid #eef2f7; 
        display: flex; align-items: center; justify-content: space-between; padding: 0 40px; 
        flex-shrink: 0; box-shadow: 0 -4px 15px rgba(0,0,0,0.02);
    }
    .btn-nav { 
        padding: 8px 25px; border-radius: 50px; font-weight: 700; 
        border: 1px solid #e2e8f0; background: #fff; color: #475569; 
        transition: all 0.2s; display: flex; align-items: center; gap: 8px; font-size: 0.95rem;
    }
    .btn-nav:hover:not(:disabled) { background: #f1f5f9; border-color: var(--primary-color); color: var(--primary-color); transform: translateY(-2px); }
    .btn-next-action { background: var(--accent-color); color: #fff; border-color: var(--accent-color); }
    .btn-next-action:hover:not(:disabled) { background: #e57d15; border-color: #e57d15; color: #fff; }

    /* Cải thiện hiển thị đáp án xem trước */
    .preview-answer-box {
        background-color: #f8f9fa;
        border-left: 3px solid var(--primary-color);
        padding: 10px;
        border-radius: 4px;
        margin-top: 10px;
    }
    .preview-answer-box.ict-preview { border-left-color: #198754; }
</style>
@endpush

<div class="exam-wrapper-fullscreen" x-data="examApp()" x-cloak>

    {{-- HEADER --}}
    <div class="exam-header">
        <div class="fw-bold d-flex align-items-center" style="font-size: 1.1rem;">
            <i class="bi bi-cpu-fill me-2 text-primary"></i> {{ $exam->title ?? 'ĐỀ THI TỐT NGHIỆP THPT' }}
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="countdown-pill shadow-sm">
                <i class="bi bi-clock-history"></i>
                <span id="countdown" style="font-variant-numeric: tabular-nums;">--:--</span>
            </div>
            <button type="button" class="btn btn-warning fw-bold px-4 py-1 rounded-pill shadow-sm" onclick="confirmSubmit()">NỘP BÀI</button>
        </div>
    </div>

    {{-- NAV BAR SỐ CÂU HỎI --}}
    <div class="question-nav-bar shadow-sm">
        <template x-for="(q, idx) in questions" :key="q.id">
            <div class="nav-item-q" 
                 :class="{ 'active': currentIdx === idx, 'done': doneList.includes(q.id.toString()) }"
                 @click="goTo(idx)" x-text="idx + 1">
            </div>
        </template>
        <template x-if="elective === ''">
            <div class="nav-item-q" :class="{ 'active': currentIdx === chungCount }" @click="goTo(chungCount)">
                <i class="bi bi-plus-lg"></i>
            </div>
        </template>
    </div>

    {{-- KHUNG CHỨA CÂU HỎI TRUNG TÂM --}}
    <div class="exam-main-area">
        <div class="question-box-container">
            
            <template x-for="(question, index) in questions" :key="question.id">
                <div class="question-main-card" x-show="currentIdx === index" x-transition.opacity.duration.300ms>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom flex-shrink-0">
                        <span class="badge bg-primary rounded-pill px-3 py-2 fs-6" x-text="'Câu ' + (index + 1)"></span>
                        <div class="text-muted small">Mã câu hỏi: <span class="fw-bold text-dark" x-text="'#' + question.id"></span></div>
                    </div>
                    
                    <div class="q-scroll-area">
                        <div class="fs-5 fw-bold mb-4 text-dark" style="line-height: 1.6;" x-html="question.content"></div>

                        <div class="q-options">
                            <template x-if="question.type === 'single_choice'">
                                <div>
                                    <template x-for="ans in question.answers" :key="ans.id">
                                        <label class="option-item">
                                            <input type="radio" :name="'answers[' + question.id + ']'" :value="ans.id" class="d-none" @change="updateDoneList()">
                                            <div class="option-box">
                                                <div class="radio-dot"></div>
                                                <span class="fw-medium text-dark" x-html="ans.content"></span>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </template>

                            <template x-if="question.type === 'true_false_group'">
                                <div class="table-responsive rounded-3 border overflow-hidden shadow-sm">
                                    <table class="table tf-table table-hover align-middle mb-0">
                                        <thead class="text-center bg-light">
                                            <tr>
                                                <th class="text-start ps-4" style="width: 70%;">Nội dung mệnh đề</th>
                                                <th style="width: 15%;">Đúng</th>
                                                <th style="width: 15%;">Sai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="child in question.children" :key="child.id">
                                                <tr>
                                                    <td class="ps-4 fw-medium text-dark" x-html="child.content"></td>
                                                    <template x-for="ans in child.answers" :key="ans.id">
                                                        <td class="text-center">
                                                            <input type="radio" :name="'answers[' + child.id + ']'" :value="ans.id" class="form-check-input shadow-none border-secondary" style="width: 22px; height: 22px; cursor: pointer;" @change="updateDoneList()">
                                                        </td>
                                                    </template>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="elective === '' && currentIdx === chungCount">
                <div class="question-main-card border-dashed border-primary" style="background: #f8faff;" x-show="currentIdx === chungCount" x-transition.opacity.duration.300ms>
                    
                    <div class="text-center mb-3 border-bottom pb-3 flex-shrink-0">
                        <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-layers-half me-2"></i>PHẦN THI TỰ CHỌN</h4>
                        <p class="text-muted mb-0 small">Hãy xem trước nội dung các câu hỏi và đáp án bên dưới để đưa ra quyết định.</p>
                    </div>

                    <ul class="nav nav-pills nav-fill gap-2 p-1 small bg-white border rounded-pill shadow-sm mb-3 flex-shrink-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link rounded-pill fw-bold border-0" :class="previewTab === 'cs' ? 'active bg-primary text-white' : 'bg-light text-dark'" @click="previewTab = 'cs'">
                                Xem Đề Khoa học máy tính (CS)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link rounded-pill fw-bold border-0" :class="previewTab === 'ict' ? 'active bg-success text-white' : 'bg-light text-dark'" @click="previewTab = 'ict'">
                                Xem Đề Tin học ứng dụng (ICT)
                            </button>
                        </li>
                    </ul>

                    <div class="q-scroll-area border rounded-3 p-3 bg-white shadow-sm mb-3">
                        
                        <div x-show="previewTab === 'cs'">
                            <template x-for="(q, idx) in rawCsQuestions" :key="'preview_cs_'+q.id">
                                <div class="mb-4 pb-3 border-bottom">
                                    <h6 class="fw-bold text-primary" x-text="'Câu ' + (chungCount + idx + 1) + ':'"></h6>
                                    <div x-html="q.content" class="text-dark fw-medium mb-2" style="font-size: 0.95rem;"></div>
                                    <div class="preview-answer-box">
                                        <template x-if="q.type === 'single_choice'">
                                            <div class="d-flex flex-column gap-1">
                                                <template x-for="ans in q.answers" :key="'prev_cs_ans_'+ans.id">
                                                    <div class="d-flex align-items-start gap-2 text-muted small">
                                                        <i class="bi bi-circle mt-1" style="font-size: 0.5rem;"></i>
                                                        <span x-html="ans.content"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="q.type === 'true_false_group'">
                                            <div class="d-flex flex-column gap-1">
                                                <template x-for="child in q.children" :key="'prev_cs_child_'+child.id">
                                                    <div class="d-flex align-items-start gap-2 text-muted small">
                                                        <i class="bi bi-check2-square mt-1"></i>
                                                        <span x-html="child.content"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div x-show="previewTab === 'ict'" x-cloak>
                            <template x-for="(q, idx) in rawIctQuestions" :key="'preview_ict_'+q.id">
                                <div class="mb-4 pb-3 border-bottom">
                                    <h6 class="fw-bold text-success" x-text="'Câu ' + (chungCount + idx + 1) + ':'"></h6>
                                    <div x-html="q.content" class="text-dark fw-medium mb-2" style="font-size: 0.95rem;"></div>
                                    <div class="preview-answer-box ict-preview">
                                        <template x-if="q.type === 'single_choice'">
                                            <div class="d-flex flex-column gap-1">
                                                <template x-for="ans in q.answers" :key="'prev_ict_ans_'+ans.id">
                                                    <div class="d-flex align-items-start gap-2 text-muted small">
                                                        <i class="bi bi-circle mt-1" style="font-size: 0.5rem;"></i>
                                                        <span x-html="ans.content"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="q.type === 'true_false_group'">
                                            <div class="d-flex flex-column gap-1">
                                                <template x-for="child in q.children" :key="'prev_ict_child_'+child.id">
                                                    <div class="d-flex align-items-start gap-2 text-muted small">
                                                        <i class="bi bi-check2-square mt-1"></i>
                                                        <span x-html="child.content"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="text-center flex-shrink-0 pt-2 border-top">
                        <p class="text-danger fw-bold small mb-2 mt-2"><i class="bi bi-exclamation-triangle"></i> Lưu ý: Bạn không thể đổi môn sau khi đã bấm xác nhận.</p>
                        <button type="button" 
                                class="btn px-5 py-2 fw-bold rounded-pill shadow text-white"
                                :class="previewTab === 'cs' ? 'btn-primary' : 'btn-success'"
                                @click="confirmAndSelectElective(previewTab)">
                            <i class="bi bi-check2-circle me-1"></i> 
                            <span x-text="previewTab === 'cs' ? 'XÁC NHẬN CHỌN KHOA HỌC MÁY TÍNH' : 'XÁC NHẬN CHỌN TIN HỌC ỨNG DỤNG'"></span>
                        </button>
                    </div>

                </div>
            </template>
        </div>
    </div>

    {{-- FOOTER ĐIỀU HƯỚNG --}}
    <div class="exam-bottom-bar">
        <button type="button" class="btn-nav" :disabled="currentIdx === 0" @click="goTo(currentIdx - 1)">
            <i class="bi bi-chevron-left"></i> Câu trước
        </button>

        <div class="d-flex align-items-center gap-2">
            <div class="text-muted fw-bold d-none d-sm-block small">Đã làm:</div>
            <div class="fw-bold px-3 py-1 rounded-pill shadow-sm" style="background: var(--primary-color); color: #fff; font-size: 0.95rem;">
                <span x-text="doneList.length"></span> / <span x-text="totalQuestions"></span>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="button" class="btn-nav btn-next-action" x-show="currentIdx < totalSteps - 1" @click="goTo(currentIdx + 1)">
                Câu tiếp <i class="bi bi-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm" x-show="elective !== '' && currentIdx === totalQuestions - 1" onclick="confirmSubmit()">
                NỘP BÀI
            </button>
        </div>
    </div>
</div>

<form id="examForm" action="{{ route('exam.submit', $session->id ?? 0) }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="exam_id_hidden" value="{{ $exam->id }}">
</form>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('examApp', () => ({
            currentIdx: 0,
            totalSteps: {{ $totalSteps }},
            totalQuestions: {{ $totalQuestions }},
            chungCount: {{ $chungCount }},
            elective: '{{ $savedElective }}',
            questions: @json($allQuestions),
            
            rawCsQuestions: @json($csQuestions),
            rawIctQuestions: @json($ictQuestions),
            previewTab: 'cs',
            
            doneList: [],

            init() { 
                this.updateDoneList(); 
                
                this.$watch('currentIdx', () => {
                    this.updateDoneList();
                });
            },
            
            // --- HÀM updateDoneList ĐÃ ĐƯỢC SỬA LẠI ĐỂ TÍNH ĐÚNG LOGIC ĐÚNG/SAI ---
            updateDoneList() {
                let done = [];
                
                // Duyệt qua từng câu hỏi đang có trong danh sách
                this.questions.forEach(q => {
                    if (q.type === 'single_choice') {
                        // Trắc nghiệm đơn: Chỉ cần 1 đáp án được chọn
                        let isChecked = document.querySelector(`input[name="answers[${q.id}]"]:checked`);
                        if (isChecked) {
                            done.push(q.id.toString());
                        }
                    } 
                    else if (q.type === 'true_false_group' && q.children) {
                        // Đúng/Sai: Phải kiểm tra xem TẤT CẢ các câu con đã được chọn chưa
                        let allAnswered = true; 
                        
                        q.children.forEach(child => {
                            let isChildChecked = document.querySelector(`input[name="answers[${child.id}]"]:checked`);
                            if (!isChildChecked) {
                                allAnswered = false; // Có ít nhất 1 câu con chưa chọn -> Chưa xong
                            }
                        });
                        
                        // Chỉ khi làm đủ 4/4 câu con (allAnswered = true) thì mới ghi nhận câu cha đã hoàn thành
                        if (allAnswered && q.children.length > 0) {
                            done.push(q.id.toString());
                        }
                    }
                });
                
                this.doneList = done;
            },
            // ----------------------------------------------------------------------
            
            goTo(idx) {
                if(idx >= 0 && idx < this.totalSteps) {
                    this.currentIdx = idx;
                    this.$nextTick(() => {
                        let navBtns = document.querySelectorAll('.nav-item-q');
                        if(navBtns[idx]) navBtns[idx].scrollIntoView({ behavior: 'smooth', inline: 'center' });
                    });
                }
            },
            
            confirmAndSelectElective(type) {
                if(confirm("Bạn có chắc chắn muốn chọn phần thi này? Khi đã chọn sẽ không thể thay đổi!")) {
                    this.elective = type;
                    
                    const newQuestions = (type === 'cs') ? this.rawCsQuestions : this.rawIctQuestions;
                    this.questions = [...this.questions, ...newQuestions];
                    
                    this.totalQuestions = this.questions.length;
                    this.totalSteps = this.totalQuestions;
                    this.currentIdx = this.chungCount;

                    fetch(`{{ route('exam.saveElective', $session->id ?? 0) }}`, {
                        method: 'POST', 
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{csrf_token()}}' },
                        body: JSON.stringify({ elective: type })
                    });

                    // Cập nhật lại danh sách đã làm sau khi load thêm câu hỏi
                    this.$nextTick(() => { this.updateDoneList(); });
                }
            }
        }));
    });

    function confirmSubmit() {
        if(confirm("Xác nhận nộp bài làm?")) {
            const form = document.getElementById('examForm');
            
            const alpineData = Alpine.$data(document.querySelector('[x-data]'));
            let electiveInput = document.createElement('input');
            electiveInput.type = 'hidden';
            electiveInput.name = 'selected_elective';
            electiveInput.value = alpineData ? alpineData.elective : '';
            form.appendChild(electiveInput);

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
        let cd = document.getElementById("countdown");
        if(cd) cd.innerText = (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
    }, 1000);
</script>
@endpush
</x-app-layout>