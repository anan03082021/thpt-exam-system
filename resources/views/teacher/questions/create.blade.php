<x-layouts.teacher title="Thêm câu hỏi mới">

    {{-- Hiển thị lỗi Validation --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error) 
                    <li>{{ $error }}</li> 
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Hiển thị lỗi Session --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ❌ {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary fw-bold mb-0">Thêm câu hỏi mới</h3>
        <a href="{{ route('teacher.questions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
        
    <form action="{{ route('teacher.questions.store') }}" method="POST" id="createQuestionForm">
        @csrf
        
        {{-- CARD 1: THÔNG TIN PHÂN LOẠI --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-white fw-bold py-3 border-bottom">
                <i class="bi bi-tags"></i> 1. Thông tin phân loại
            </div>
            <div class="card-body bg-light">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Lớp <span class="text-danger">*</span></label>
                        <select name="grade" class="form-select">
                            <option value="10">Lớp 10</option>
                            <option value="11">Lớp 11</option>
                            <option value="12">Lớp 12</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Định hướng <span class="text-danger">*</span></label>
                        <select name="orientation" class="form-select">
                            <option value="chung">Chung</option>
                            <option value="ict">ICT</option>
                            <option value="cs">CS</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Chủ đề <span class="text-danger">*</span></label>
                        <select name="topic_id" class="form-select" required>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Năng lực (Chung)</label>
                        <select name="competency_id" class="form-select" required>
                            @foreach($competencies as $comp)
                                <option value="{{ $comp->id }}" title="{{ $comp->description }}">
                                    {{ $comp->code }}: {{ Str::limit($comp->description, 30) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Loại câu hỏi <span class="text-danger">*</span></label>
                        <select name="type" id="questionType" class="form-select" onchange="toggleForm()">
                            <option value="single_choice">Dạng 1: Trắc nghiệm</option>
                            <option value="true_false_group">Dạng 2: Đúng/Sai</option>
                        </select>
                    </div>

                    <div class="col-md-4" id="mainLevelDiv">
                        <label class="form-label fw-bold">Mức độ nhận thức <span class="text-danger">*</span></label>
                        <select name="cognitive_level_id" class="form-select">
                            <option value="">-- Chọn mức độ --</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: NỘI DUNG CHI TIẾT --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3 border-bottom">
                <i class="bi bi-pencil-square"></i> 2. Nội dung chi tiết
            </div>
            <div class="card-body">
                
                {{-- FORM DẠNG 1: TRẮC NGHIỆM --}}
                <div id="formSingleChoice">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="3" placeholder="Nhập nội dung câu hỏi..."></textarea>
                    </div>
                    
                    <label class="form-label fw-bold text-primary mb-3">Các phương án trả lời (Chọn đáp án đúng):</label>
                    <div class="row g-3">
                        @for($i = 1; $i <= 4; $i++)
                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="input-group-text bg-white">
                                        <input class="form-check-input mt-0" type="radio" name="correct_answer" value="{{ $i }}" {{ $i==1 ? 'checked' : '' }} style="cursor: pointer;">
                                    </div>
                                    <input type="text" name="answers[{{ $i }}]" class="form-control" placeholder="Đáp án {{ $i }}" required>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- FORM DẠNG 2: ĐÚNG/SAI CHÙM --}}
                <div id="formTrueFalse" style="display: none;">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Đoạn văn dẫn (Không có mức độ) <span class="text-danger">*</span></label>
                        <textarea id="tf_content" class="form-control" rows="3" placeholder="Nhập đoạn văn dẫn cho nhóm câu hỏi..."></textarea>
                        {{-- Lưu ý: Nội dung này sẽ được copy sang textarea[name="content"] khi submit --}}
                    </div>
                    
                    <label class="form-label fw-bold text-primary mb-3">4 Ý nhận định con (Chọn mức độ cho từng ý):</label>
                    
                    <div class="row">
                        @for($i = 0; $i < 4; $i++)
                            <div class="col-md-12">
                                <div class="card mb-3 bg-light border-0">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center g-2">
                                            <div class="col-md-1 text-center fw-bold text-secondary">
                                                Ý {{ $i+1 }}
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="sub_questions[{{ $i }}][content]" class="form-control" placeholder="Nội dung ý nhận định...">
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <select name="sub_questions[{{ $i }}][cognitive_level_id]" class="form-select">
                                                    <option value="">-- Mức độ --</option>
                                                    @foreach($levels as $level)
                                                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <select name="sub_questions[{{ $i }}][correct_option]" class="form-select fw-bold text-center">
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
                    <button type="submit" class="btn btn-primary px-5 fw-bold">
                        <i class="bi bi-save"></i> Lưu câu hỏi
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- SCRIPT XỬ LÝ LOGIC ẨN HIỆN FORM --}}
    <script>
        function toggleForm() {
            var type = document.getElementById('questionType').value;
            var formSingle = document.getElementById('formSingleChoice');
            var formTF = document.getElementById('formTrueFalse');
            var mainLevelDiv = document.getElementById('mainLevelDiv');
            var mainLevelSelect = mainLevelDiv.querySelector('select');
            
            var inputsSingle = formSingle.querySelectorAll('input, textarea');
            var inputsTF = formTF.querySelectorAll('input, textarea, select');

            if (type === 'single_choice') {
                // Hiển thị Form Trắc nghiệm
                formSingle.style.display = 'block';
                formTF.style.display = 'none';
                
                // Hiển thị & Bắt buộc nhập Mức độ chung
                mainLevelDiv.style.visibility = 'visible'; 
                mainLevelSelect.required = true; 
                if(mainLevelSelect.value === "") mainLevelSelect.value = ""; // Reset nếu cần

                // Enable/Disable inputs để tránh gửi dữ liệu thừa
                inputsSingle.forEach(el => el.disabled = false);
                inputsTF.forEach(el => el.disabled = true);

            } else {
                // Hiển thị Form Đúng/Sai
                formSingle.style.display = 'none';
                formTF.style.display = 'block';
                
                // Ẩn & Không bắt buộc Mức độ chung (vì mức độ nằm ở từng ý con)
                mainLevelDiv.style.visibility = 'hidden';  
                mainLevelSelect.required = false;

                // Enable/Disable inputs
                inputsSingle.forEach(el => el.disabled = true);
                inputsTF.forEach(el => el.disabled = false);
            }
        }

        // Xử lý trước khi Submit form
        document.getElementById('createQuestionForm').addEventListener('submit', function(e) {
            var type = document.getElementById('questionType').value;
            
            // Nếu là dạng Đúng/Sai -> Copy nội dung từ ô tf_content sang ô content chính
            if (type === 'true_false_group') {
                var mainContent = document.querySelector('textarea[name="content"]');
                var tfContent = document.getElementById('tf_content');
                
                mainContent.value = tfContent.value; // Copy giá trị
                mainContent.disabled = false; // Enable lại để gửi được dữ liệu đi
            }
        });
        
        // Chạy hàm 1 lần khi load trang để set trạng thái ban đầu
        document.addEventListener("DOMContentLoaded", function() {
            toggleForm();
        });
    </script>

</x-layouts.teacher>