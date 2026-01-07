<x-layouts.teacher title="Thêm câu hỏi mới">

    @push('styles')
    <style>
        .form-select:focus, .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25); }
        .quick-input-area { background-color: #f0fdf4; border: 2px dashed #16a34a; border-radius: 8px; }
        .guide-text { font-size: 0.9rem; color: #15803d; }
        .guide-badge { font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; font-weight: bold; }
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
                {{-- (Giữ nguyên phần chọn Lớp, Chủ đề, YCCD...) --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Lớp <span class="text-danger">*</span></label>
                        <select name="grade" class="form-select" required>
                            <option value="">-- Chọn --</option>
                            <option value="10">Lớp 10</option>
                            <option value="11">Lớp 11</option>
                            <option value="12">Lớp 12</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Định hướng <span class="text-danger">*</span></label>
                        <select name="orientation" class="form-select" required>
                            <option value="chung">Chung</option>
                            <option value="ict">ICT</option>
                            <option value="cs">CS</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Chủ đề <span class="text-danger">*</span></label>
                        <select name="topic_id" id="topicSelect" class="form-select" required>
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($topics as $topic) <option value="{{ $topic->id }}">{{ $topic->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary">Nội dung cốt lõi</label>
                        <select name="core_content_id" id="coreContentSelect" class="form-select bg-white"><option value="">-- Vui lòng chọn chủ đề trước --</option></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary">Yêu cầu cần đạt</label>
                        <select name="learning_objective_id" id="objectiveSelect" class="form-select bg-white"><option value="">-- Vui lòng chọn chủ đề trước --</option></select>
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
                        {{-- Khi đổi loại câu hỏi -> Đổi hướng dẫn nhập nhanh --}}
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
                            {{-- Hướng dẫn thay đổi theo loại câu hỏi --}}
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
                        <textarea id="tf_content" class="form-control"></textarea> {{-- Dùng CKEditor cho TF luôn --}}
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
    {{-- Import thư viện CKEditor --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>

    <script>
        let myEditor; // Editor cho Trắc nghiệm
        let tfEditor; // Editor cho Đúng/Sai

        // --- 1. CẤU HÌNH UPLOAD ẢNH ---
        const editorConfig = {
            toolbar: [
                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 
                '|', 'insertTable', 'blockQuote', 'codeBlock', 'imageUpload', '|', 'undo', 'redo'
            ],
            simpleUpload: {
                uploadUrl: "{{ route('teacher.questions.upload_image') }}",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }
        };

        // --- 2. KHỞI TẠO EDITOR ---
        ClassicEditor.create(document.querySelector('#editorContent'), editorConfig)
            .then(e => { myEditor = e; })
            .catch(err => console.error(err));

        ClassicEditor.create(document.querySelector('#tf_content'), editorConfig)
            .then(e => { tfEditor = e; })
            .catch(err => console.error(err));


        // --- 3. DATA & LOGIC LỌC CHỦ ĐỀ (Giữ nguyên) ---
        const topicsData = @json($topics);
        const topicSelect = document.getElementById('topicSelect');
        const coreSelect = document.getElementById('coreContentSelect');
        const objSelect = document.getElementById('objectiveSelect');

        topicSelect.addEventListener('change', function() {
            const selectedId = parseInt(this.value);
            coreSelect.innerHTML = '<option value="">-- Chọn nội dung --</option>';
            objSelect.innerHTML = '<option value="">-- Chọn yêu cầu --</option>';
            if (!selectedId) return;
            const topic = topicsData.find(t => t.id === selectedId);
            if (topic) {
                if (topic.core_contents) topic.core_contents.forEach(c => coreSelect.add(new Option(c.name, c.id)));
                if (topic.learning_objectives) topic.learning_objectives.forEach(o => objSelect.add(new Option(o.content.substring(0,90), o.id)));
            }
        });

        // --- 4. TOGGLE FORM (ẨN HIỆN) ---
        function toggleFormType() {
            var type = document.getElementById('questionType').value;
            var formSingle = document.getElementById('formSingleChoice');
            var formTF = document.getElementById('formTrueFalse');
            var mainLevelDiv = document.getElementById('mainLevelDiv');
            
            // Hướng dẫn nhập nhanh
            document.getElementById('guideSingle').style.display = (type === 'single_choice') ? 'block' : 'none';
            document.getElementById('guideTF').style.display = (type === 'true_false_group') ? 'block' : 'none';

            // Placeholder nhập nhanh
            const rawInput = document.getElementById('rawInput');
            if (type === 'single_choice') {
                rawInput.placeholder = "Ví dụ:\nPython là gì?\nA. Rắn\n*B. Ngôn ngữ lập trình\nC. Món ăn\nD. Loại xe";
                formSingle.style.display = 'block';
                formTF.style.display = 'none';
                mainLevelDiv.style.visibility = 'visible';
            } else {
                rawInput.placeholder = "Ví dụ:\nCho đoạn code...\n+ Ý đúng\n- Ý sai\n+ Ý đúng\n- Ý sai";
                formSingle.style.display = 'none';
                formTF.style.display = 'block';
                mainLevelDiv.style.visibility = 'hidden';
            }
        }

        function toggleQuickMode() {
            var isChecked = document.getElementById('quickModeSwitch').checked;
            document.getElementById('quickInputSection').style.display = isChecked ? 'block' : 'none';
        }

        // --- 5. HÀM PHÂN TÍCH (AUTO PARSE) ---
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
                    text = text.replace(/^[A-D0-9][\.\)]\s*/i, ''); // Xóa A. B.
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
                    text = text.replace(/^[0-9][\.\)]\s*/i, ''); // Xóa 1. 2.
                    document.getElementById('tf_item_content_' + index).value = text;
                    let selectBox = document.getElementById('tf_item_correct_' + index);
                    if (isTrue) selectBox.value = 'true';
                    else if (isFalse) selectBox.value = 'false';
                });
            }
            alert("Đã phân tích xong! Hãy kiểm tra lại trước khi lưu.");
        }

        // --- 6. XỬ LÝ SỰ KIỆN SUBMIT (QUAN TRỌNG NHẤT) ---
        document.getElementById('createQuestionForm').addEventListener('submit', function(e) {
            let type = document.getElementById('questionType').value;
            let singleChoiceTextarea = document.getElementById('editorContent');

            // TRƯỜNG HỢP 1: TRẮC NGHIỆM
            if (type === 'single_choice') {
                // Đảm bảo textarea gốc nhận dữ liệu từ CKEditor
                if (myEditor) {
                    let data = myEditor.getData();
                    if (!data) {
                        e.preventDefault();
                        alert("Vui lòng nhập nội dung câu hỏi!");
                        return;
                    }
                    singleChoiceTextarea.value = data;
                }
                // Đảm bảo có name="content"
                singleChoiceTextarea.setAttribute('name', 'content');
            } 
            
            // TRƯỜNG HỢP 2: ĐÚNG/SAI CHÙM
            else if (type === 'true_false_group') {
                // Xóa name của trắc nghiệm để không bị gửi thừa
                singleChoiceTextarea.removeAttribute('name');

                // Lấy dữ liệu từ editor Đúng/Sai
                if (tfEditor) {
                    let data = tfEditor.getData();
                    if (!data) {
                        e.preventDefault();
                        alert("Vui lòng nhập đoạn văn dẫn!");
                        return;
                    }
                    // Tạo input ẩn để gửi content đi
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