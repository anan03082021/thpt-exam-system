<x-layouts.teacher title="Chỉnh sửa câu hỏi #{{ $question->id }}">

    @push('styles')
    <style>
        .form-select:focus, .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25); }
    </style>
    @endpush

    {{-- Hiển thị lỗi Validation --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-warning fw-bold mb-0">✏️ Chỉnh sửa câu hỏi #{{ $question->id }}</h3>
        <a href="{{ route('teacher.questions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('teacher.questions.update', $question->id) }}" method="POST" id="editQuestionForm">
        @csrf
        @method('PUT')
        
        {{-- CARD 1: THÔNG TIN PHÂN LOẠI --}}
        <div class="card shadow-sm mb-4 border-0 rounded-4">
            <div class="card-header bg-white fw-bold py-3 border-bottom">
                <i class="bi bi-tags"></i> 1. Thông tin phân loại
            </div>
            <div class="card-body bg-light">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Lớp <span class="text-danger">*</span></label>
                        <select name="grade" class="form-select" required>
                            <option value="10" {{ $question->grade == 10 ? 'selected' : '' }}>Lớp 10</option>
                            <option value="11" {{ $question->grade == 11 ? 'selected' : '' }}>Lớp 11</option>
                            <option value="12" {{ $question->grade == 12 ? 'selected' : '' }}>Lớp 12</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Định hướng <span class="text-danger">*</span></label>
                        <select name="orientation" class="form-select" required>
                            <option value="chung" {{ $question->orientation == 'chung' ? 'selected' : '' }}>Chung</option>
                            <option value="ict" {{ $question->orientation == 'ict' ? 'selected' : '' }}>ICT</option>
                            <option value="cs" {{ $question->orientation == 'cs' ? 'selected' : '' }}>CS</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Chủ đề <span class="text-danger">*</span></label>
                        <select name="topic_id" id="topicSelect" class="form-select" required>
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ $question->topic_id == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Hàng 2: Nội dung cốt lõi & Yêu cầu cần đạt [MỚI] --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary">Nội dung cốt lõi</label>
                        {{-- Data-selected để JS biết giá trị cũ cần fill --}}
                        <select name="core_content_id" id="coreContentSelect" class="form-select bg-white" data-selected="{{ $question->core_content_id }}">
                            <option value="">-- Đang tải dữ liệu... --</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary">Yêu cầu cần đạt</label>
                        <select name="learning_objective_id" id="objectiveSelect" class="form-select bg-white" data-selected="{{ $question->learning_objective_id }}">
                            <option value="">-- Đang tải dữ liệu... --</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Năng lực <span class="text-danger">*</span></label>
                        <select name="competency_id" class="form-select" required>
                            @foreach($competencies as $comp)
                                <option value="{{ $comp->id }}" {{ $question->competency_id == $comp->id ? 'selected' : '' }}>{{ $comp->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Loại câu hỏi</label>
                        <input type="text" class="form-control bg-secondary bg-opacity-10" value="{{ $question->type == 'single_choice' ? 'Trắc nghiệm' : 'Đúng/Sai' }}" disabled readonly>
                        <input type="hidden" name="type" value="{{ $question->type }}" id="questionType">
                    </div>
                    
                    {{-- Chỉ hiện mức độ chung nếu là Trắc nghiệm --}}
                    @if($question->type == 'single_choice')
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Mức độ <span class="text-danger">*</span></label>
                        <select name="cognitive_level_id" class="form-select" required>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ $question->cognitive_level_id == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- CARD 2: NỘI DUNG CHI TIẾT --}}
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white fw-bold py-3 border-bottom">
                <i class="bi bi-pencil-square"></i> 2. Nội dung chi tiết
            </div>
            <div class="card-body">
                
                {{-- CKEDITOR CHO NỘI DUNG --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">
                        {{ $question->type == 'single_choice' ? 'Nội dung câu hỏi' : 'Đoạn văn dẫn' }} <span class="text-danger">*</span>
                    </label>
                    <textarea name="content" id="editorContent" class="form-control" rows="3">{{ $question->content }}</textarea>
                </div>

                {{-- TRƯỜNG HỢP 1: TRẮC NGHIỆM --}}
                @if($question->type == 'single_choice')
                    <label class="form-label fw-bold text-primary mb-3">Các phương án trả lời:</label>
                    <div class="row g-3">
                        @foreach($question->answers as $index => $ans)
                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="input-group-text bg-white">
                                        <input class="form-check-input mt-0" type="radio" name="correct_answer" value="{{ $index }}" {{ $ans->is_correct ? 'checked' : '' }} style="cursor: pointer;">
                                    </div>
                                    <input type="text" name="answers[{{ $index }}]" class="form-control" value="{{ $ans->content }}" required>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- TRƯỜNG HỢP 2: ĐÚNG/SAI CHÙM --}}
                @if($question->type == 'true_false_group')
                    <label class="form-label fw-bold text-primary mb-3">4 Ý nhận định con:</label>
                    <div class="row">
                        @foreach($question->children as $i => $child)
                            @php
                                $correctAnswer = $child->answers->where('is_correct', true)->first();
                                $isTrue = $correctAnswer && $correctAnswer->content == 'Đúng';
                            @endphp
                            <div class="col-md-12">
                                <div class="card mb-3 bg-light border-0">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center g-2">
                                            <div class="col-md-1 text-center fw-bold text-secondary">Ý {{ $i+1 }}</div>
                                            <div class="col-md-6">
                                                <input type="text" name="sub_questions[{{ $i }}][content]" class="form-control" value="{{ $child->content }}" placeholder="Nội dung ý..." required>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="sub_questions[{{ $i }}][cognitive_level_id]" class="form-select form-select-sm">
                                                    @foreach($levels as $level)
                                                        <option value="{{ $level->id }}" {{ $child->cognitive_level_id == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <select name="sub_questions[{{ $i }}][correct_option]" class="form-select fw-bold text-center">
                                                    <option value="true" class="text-success" {{ $isTrue ? 'selected' : '' }}>ĐÚNG</option>
                                                    <option value="false" class="text-danger" {{ !$isTrue ? 'selected' : '' }}>SAI</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4 pt-3 border-top text-end">
                    <a href="{{ route('teacher.questions.index') }}" class="btn btn-secondary me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-warning px-5 fw-bold text-dark">
                        <i class="bi bi-pencil-square"></i> Cập nhật câu hỏi
                    </button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    {{-- Import CKEditor --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>

    <script>
        let myEditor;

        // --- 1. KHỞI TẠO CKEDITOR ---
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

        ClassicEditor
            .create(document.querySelector('#editorContent'), editorConfig)
            .then(e => { myEditor = e; })
            .catch(err => console.error(err));

        // --- 2. LOGIC LOAD DỮ LIỆU PHỤ THUỘC (TOPIC -> YCCD) ---
        const topicsData = @json($topics);
        const topicSelect = document.getElementById('topicSelect');
        const coreSelect = document.getElementById('coreContentSelect');
        const objSelect = document.getElementById('objectiveSelect');

        function loadTopicDependencies(topicId, selectedCore = null, selectedObj = null) {
            coreSelect.innerHTML = '<option value="">-- Chọn nội dung --</option>';
            objSelect.innerHTML = '<option value="">-- Chọn yêu cầu --</option>';

            if (!topicId) return;
            const topic = topicsData.find(t => t.id === parseInt(topicId));

            if (topic) {
                // Load Nội dung cốt lõi
                if (topic.core_contents) {
                    topic.core_contents.forEach(c => {
                        let selected = (selectedCore == c.id) ? 'selected' : '';
                        coreSelect.add(new Option(c.name, c.id, false, selected == 'selected'));
                    });
                }
                // Load Yêu cầu cần đạt
                if (topic.learning_objectives) {
                    topic.learning_objectives.forEach(o => {
                        let text = o.content.length > 100 ? o.content.substring(0, 100) + '...' : o.content;
                        let selected = (selectedObj == o.id) ? 'selected' : '';
                        let option = new Option(text, o.id, false, selected == 'selected');
                        option.title = o.content;
                        objSelect.add(option);
                    });
                }
            }
        }

        // Sự kiện khi đổi chủ đề
        topicSelect.addEventListener('change', function() {
            loadTopicDependencies(this.value);
        });

        // Chạy ngay khi load trang để fill dữ liệu cũ
        document.addEventListener("DOMContentLoaded", function() {
            let currentTopic = topicSelect.value;
            let oldCore = coreSelect.getAttribute('data-selected');
            let oldObj = objSelect.getAttribute('data-selected');
            
            if(currentTopic) {
                loadTopicDependencies(currentTopic, oldCore, oldObj);
            }
        });

        // --- 3. XỬ LÝ SUBMIT ---
        document.getElementById('editQuestionForm').addEventListener('submit', function(e) {
            if (myEditor) {
                let data = myEditor.getData();
                document.querySelector('#editorContent').value = data;
            }
        });
    </script>
    @endpush

</x-layouts.teacher>