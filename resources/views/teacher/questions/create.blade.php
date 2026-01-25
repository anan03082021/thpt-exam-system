<x-layouts.teacher title="Thêm câu hỏi mới">

    @push('styles')
    <style>
        .form-select:focus, .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25); }
        .quick-input-area { background-color: #f0fdf4; border: 2px dashed #16a34a; border-radius: 8px; }
        .guide-text { font-size: 0.9rem; color: #15803d; }
    </style>
    @endpush

    {{-- Hiển thị lỗi --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary fw-bold mb-0">Thêm câu hỏi mới</h3>
        <a href="{{ route('teacher.questions.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>
        
    <form action="{{ route('teacher.questions.store') }}" method="POST" id="createQuestionForm">
        @csrf
        
        {{-- CARD 1: THÔNG TIN PHÂN LOẠI --}}
        <div class="card shadow-sm mb-4 border-0 rounded-4">
            <div class="card-header bg-white fw-bold py-3 border-bottom"><i class="bi bi-tags"></i> 1. Thông tin phân loại</div>
            <div class="card-body bg-light">
                <div class="row mb-3">
                    {{-- [SỬA] Thêm ID để JS bắt sự kiện --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Lớp <span class="text-danger">*</span></label>
                        <select name="grade" id="gradeSelect" class="form-select" required>
                            <option value="">-- Chọn --</option>
                            <option value="10">Lớp 10</option>
                            <option value="11">Lớp 11</option>
                            <option value="12">Lớp 12</option>
                        </select>
                    </div>
                    {{-- [SỬA] Thêm ID để JS bắt sự kiện --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Định hướng <span class="text-danger">*</span></label>
                        <select name="orientation" id="orientationSelect" class="form-select" required>
                            <option value="chung">Chung (Bắt buộc)</option>
                            <option value="cs">Khoa học máy tính (CS)</option>
                            <option value="ict">Tin học ứng dụng (ICT)</option>
                        </select>
                    </div>
                    {{-- [SỬA] Xóa foreach, để disabled mặc định --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Chủ đề <span class="text-danger">*</span></label>
                        <select name="topic_id" id="topicSelect" class="form-select" disabled required>
                            <option value="">-- Vui lòng chọn Lớp & Định hướng trước --</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary">Nội dung cốt lõi</label>
                        <select name="core_content_id" id="coreContentSelect" class="form-select bg-white" disabled><option value="">-- Chọn chủ đề trước --</option></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary">Yêu cầu cần đạt</label>
                        <select name="learning_objective_id" id="objectiveSelect" class="form-select bg-white" disabled><option value="">-- Chọn nội dung trước --</option></select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Năng lực <span class="text-danger">*</span></label>
                        <select name="competency_id" class="form-select" required>
                            @foreach($competencies as $comp) <option value="{{ $comp->id }}">{{ $comp->code }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Loại câu hỏi <span class="text-danger">*</span></label>
                        <select name="type" id="questionType" class="form-select" onchange="toggleFormType()">
                            <option value="single_choice">Dạng 1: Trắc nghiệm</option>
                            <option value="true_false_group">Dạng 2: Đúng/Sai</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="mainLevelDiv">
                        <label class="form-label fw-bold">Mức độ <span class="text-danger">*</span></label>
                        <select name="cognitive_level_id" class="form-select">
                            @foreach($levels as $level) <option value="{{ $level->id }}">{{ $level->name }}</option> @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: NỘI DUNG CHI TIẾT --}}
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white fw-bold py-3 border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pencil-square"></i> 2. Nội dung chi tiết</span>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="quickModeSwitch" onchange="toggleQuickMode()">
                    <label class="form-check-label fw-bold text-success" for="quickModeSwitch">
                        <i class="bi bi-lightning-fill"></i> Nhập nhanh (Auto Parse)
                    </label>
                </div>
            </div>
            
            <div class="card-body">
                
                {{-- VÙNG NHẬP NHANH --}}
                <div id="quickInputSection" style="display: none;">
                    <div class="quick-input-area p-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label fw-bold text-success">Dán nội dung vào đây:</label>
                            <div id="guideSingle" class="guide-text">
                                <span class="bg-white border px-1 rounded">Quy tắc Trắc nghiệm:</span> 
                                Đánh dấu sao <code>*</code> trước đáp án đúng.
                            </div>
                            <div id="guideTF" class="guide-text" style="display: none;">
                                <span class="bg-white border px-1 rounded">Quy tắc Đúng/Sai:</span> 
                                Dấu <code>+</code> là Đúng, <code>-</code> là Sai.
                            </div>
                        </div>
                        
                        <textarea id="rawInput" class="form-control mb-3 font-monospace" rows="6" placeholder="Nhập nội dung ở đây..."></textarea>
                        
                        <button type="button" class="btn btn-success btn-sm fw-bold" onclick="parseQuestion()">
                            <i class="bi bi-arrow-down-circle"></i> Phân tích ngay
                        </button>
                    </div>
                    <hr>
                </div>

                {{-- FORM TRẮC NGHIỆM --}}
                <div id="formSingleChoice">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                        <textarea name="content" id="editorContent" class="form-control"></textarea>
                    </div>
                    <label class="form-label fw-bold text-primary mb-3">Các phương án trả lời:</label>
                    <div class="row g-3">
                        @for($i = 1; $i <= 4; $i++)
                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="input-group-text bg-white">
                                        <input class="form-check-input mt-0" type="radio" name="correct_answer" value="{{ $i-1 }}" id="radio_ans_{{ $i-1 }}" {{ $i==1 ? 'checked' : '' }}>
                                    </div>
                                    <input type="text" name="answers[{{ $i-1 }}]" id="input_ans_{{ $i-1 }}" class="form-control" placeholder="Đáp án {{ $i }}" required>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- FORM ĐÚNG/SAI --}}
                <div id="formTrueFalse" style="display: none;">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Đoạn văn dẫn <span class="text-danger">*</span></label>
                        <textarea id="tf_content" class="form-control"></textarea>
                    </div>
                    <label class="form-label fw-bold text-primary mb-3">4 Ý nhận định con:</label>
                    <div class="row">
                        @for($i = 0; $i < 4; $i++)
                            <div class="col-md-12">
                                <div class="card mb-3 bg-light border-0">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center g-2">
                                            <div class="col-md-1 text-center fw-bold">Ý {{ $i+1 }}</div>
                                            <div class="col-md-6">
                                                <input type="text" name="sub_questions[{{ $i }}][content]" id="tf_item_content_{{ $i }}" class="form-control" placeholder="Nội dung ý...">
                                            </div>
                                            <div class="col-md-3">
                                                <select name="sub_questions[{{ $i }}][cognitive_level_id]" class="form-select">
                                                    <option value="">-- Mức độ --</option>
                                                    @foreach($levels as $level) <option value="{{ $level->id }}">{{ $level->name }}</option> @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <select name="sub_questions[{{ $i }}][correct_option]" id="tf_item_correct_{{ $i }}" class="form-select fw-bold text-center">
                                                    <option value="true" class="text-success">ĐÚNG</option>
                                                    <option value="false" class="text-danger">SAI</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <a href="{{ route('teacher.questions.index') }}" class="btn btn-secondary me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary px-5 fw-bold"><i class="bi bi-save"></i> Lưu câu hỏi</button>
                </div>
            </div>
        </div>
    </form>

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>

    <script>
        let myEditor;
        let tfEditor;

        // --- 1. CẤU HÌNH EDITOR ---
        const editorConfig = {
            toolbar: [
                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 
                '|', 'insertTable', 'blockQuote', 'codeBlock', 'imageUpload', '|', 'undo', 'redo'
            ],
            simpleUpload: {
                uploadUrl: "{{ route('teacher.questions.upload_image') }}",
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            }
        };

        ClassicEditor.create(document.querySelector('#editorContent'), editorConfig)
            .then(e => { myEditor = e; }).catch(err => console.error(err));
        
        ClassicEditor.create(document.querySelector('#tf_content'), editorConfig)
            .then(e => { tfEditor = e; }).catch(err => console.error(err));

        // --- 2. LOGIC LỌC CHỦ ĐỀ MỚI (SỬ DỤNG AJAX - KHÔNG DÙNG $topics CŨ) ---
        const els = {
            grade: document.getElementById('gradeSelect'),
            orient: document.getElementById('orientationSelect'),
            topic: document.getElementById('topicSelect'),
            core: document.getElementById('coreContentSelect'),
            obj: document.getElementById('objectiveSelect')
        };

        const resetSelect = (el, msg) => {
            el.innerHTML = `<option value="">${msg}</option>`;
            el.disabled = true;
        };

        // Khi đổi Lớp hoặc Định hướng -> Gọi API lấy Chủ đề
        const loadTopics = () => {
            const g = els.grade.value;
            const o = els.orient.value;

            resetSelect(els.topic, '-- Đang tải... --');
            resetSelect(els.core, '-- Chọn chủ đề trước --');
            resetSelect(els.obj, '-- Chọn nội dung trước --');

            if (!g) {
                resetSelect(els.topic, '-- Vui lòng chọn Lớp & Định hướng trước --');
                return;
            }

            fetch(`/api/topics?grade=${g}&orientation=${o}`)
                .then(r => r.json())
                .then(data => {
                    els.topic.innerHTML = '<option value="">-- Chọn chủ đề --</option>';
                    data.forEach(t => {
                        els.topic.innerHTML += `<option value="${t.id}">${t.name}</option>`;
                    });
                    els.topic.disabled = false;
                });
        };

        els.grade.addEventListener('change', loadTopics);
        els.orient.addEventListener('change', loadTopics);

        // Khi chọn Chủ đề -> Gọi API lấy Nội dung cốt lõi
        els.topic.addEventListener('change', function() {
            const tId = this.value;
            const g = els.grade.value;
            const o = els.orient.value;
            
            resetSelect(els.core, '-- Đang tải... --');
            resetSelect(els.obj, '-- Chọn nội dung trước --');
            
            if (!tId) return;

            fetch(`/api/core-contents?topic_id=${tId}&grade=${g}&orientation=${o}`)
                .then(r => r.json())
                .then(data => {
                    els.core.innerHTML = '<option value="">-- Chọn nội dung cốt lõi --</option>';
                    data.forEach(c => {
                        els.core.innerHTML += `<option value="${c.id}">${c.name}</option>`;
                    });
                    els.core.disabled = false;
                });
        });

        // Khi chọn Nội dung -> Gọi API lấy YCCĐ
        els.core.addEventListener('change', function() {
            const cId = this.value;
            resetSelect(els.obj, '-- Đang tải... --');
            if (!cId) return;

            fetch(`/api/learning-objectives?core_content_id=${cId}`)
                .then(r => r.json())
                .then(data => {
                    els.obj.innerHTML = '<option value="">-- Chọn yêu cầu cần đạt --</option>';
                    data.forEach(obj => {
                        const text = obj.content.length > 90 ? obj.content.substring(0,90)+'...' : obj.content;
                        els.obj.innerHTML += `<option value="${obj.id}" title="${obj.content}">${text}</option>`;
                    });
                    els.obj.disabled = false;
                });
        });

        // --- 3. CÁC HÀM XỬ LÝ GIAO DIỆN KHÁC (GIỮ NGUYÊN) ---
        function toggleFormType() {
            var type = document.getElementById('questionType').value;
            var formSingle = document.getElementById('formSingleChoice');
            var formTF = document.getElementById('formTrueFalse');
            var mainLevelDiv = document.getElementById('mainLevelDiv');
            
            document.getElementById('guideSingle').style.display = (type === 'single_choice') ? 'block' : 'none';
            document.getElementById('guideTF').style.display = (type === 'true_false_group') ? 'block' : 'none';

            if (type === 'single_choice') {
                document.getElementById('rawInput').placeholder = "Ví dụ:\nPython là gì?\nA. Rắn\n*B. Ngôn ngữ lập trình\nC. Món ăn\nD. Loại xe";
                formSingle.style.display = 'block';
                formTF.style.display = 'none';
                mainLevelDiv.style.visibility = 'visible';
            } else {
                document.getElementById('rawInput').placeholder = "Ví dụ:\nCho đoạn code...\n+ Ý đúng\n- Ý sai\n+ Ý đúng\n- Ý sai";
                formSingle.style.display = 'none';
                formTF.style.display = 'block';
                mainLevelDiv.style.visibility = 'hidden';
            }
        }

        function toggleQuickMode() {
            var isChecked = document.getElementById('quickModeSwitch').checked;
            document.getElementById('quickInputSection').style.display = isChecked ? 'block' : 'none';
        }

        function parseQuestion() {
            let rawText = document.getElementById('rawInput').value.trim();
            if (!rawText) { alert("Chưa nhập nội dung!"); return; }
            let lines = rawText.split('\n').filter(line => line.trim() !== '');
            let type = document.getElementById('questionType').value;

            if (type === 'single_choice') {
                if (lines.length < 5) { alert("Cần ít nhất 1 dòng câu hỏi và 4 đáp án!"); return; }
                let rawAnswers = lines.splice(-4);
                let questionContent = lines.join('\n');
                if (myEditor) myEditor.setData(questionContent);

                rawAnswers.forEach((line, index) => {
                    let text = line.trim();
                    let isCorrect = text.startsWith('*');
                    if (isCorrect) text = text.substring(1).trim();
                    text = text.replace(/^[A-D0-9][\.\)]\s*/i, ''); 
                    document.getElementById('input_ans_' + index).value = text;
                    if (isCorrect) document.getElementById('radio_ans_' + index).checked = true;
                });
            } else {
                if (lines.length < 5) { alert("Cần ít nhất 1 dòng dẫn và 4 ý nhận định!"); return; }
                let rawSubQuestions = lines.splice(-4);
                let mainContent = lines.join('\n');
                if (tfEditor) tfEditor.setData(mainContent);

                rawSubQuestions.forEach((line, index) => {
                    let text = line.trim();
                    let isTrue = text.startsWith('+');
                    let isFalse = text.startsWith('-');
                    if (isTrue || isFalse) text = text.substring(1).trim();
                    text = text.replace(/^[0-9][\.\)]\s*/i, ''); 
                    document.getElementById('tf_item_content_' + index).value = text;
                    let selectBox = document.getElementById('tf_item_correct_' + index);
                    if (isTrue) selectBox.value = 'true';
                    else if (isFalse) selectBox.value = 'false';
                });
            }
            alert("Đã phân tích xong! Hãy kiểm tra lại trước khi lưu.");
        }

        document.getElementById('createQuestionForm').addEventListener('submit', function(e) {
            let type = document.getElementById('questionType').value;
            let singleChoiceTextarea = document.getElementById('editorContent');

            if (type === 'single_choice') {
                if (myEditor) {
                    let data = myEditor.getData();
                    if (!data) { e.preventDefault(); alert("Vui lòng nhập nội dung câu hỏi!"); return; }
                    singleChoiceTextarea.value = data;
                }
                singleChoiceTextarea.setAttribute('name', 'content');
            } else if (type === 'true_false_group') {
                singleChoiceTextarea.removeAttribute('name');
                if (tfEditor) {
                    let data = tfEditor.getData();
                    if (!data) { e.preventDefault(); alert("Vui lòng nhập đoạn văn dẫn!"); return; }
                    let hiddenInput = document.createElement("input");
                    hiddenInput.type = "hidden";
                    hiddenInput.name = "content";
                    hiddenInput.value = data;
                    this.appendChild(hiddenInput);
                }
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            toggleFormType();
        });
    </script>
    @endpush

</x-layouts.teacher>