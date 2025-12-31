<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm câu hỏi mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">❌ {{ session('error') }}</div>
        @endif

        <h3 class="mb-4 text-primary fw-bold">Thêm câu hỏi mới</h3>
        
<form action="{{ route('teacher.questions.store') }}" method="POST">
    @csrf
    
    <div class="card shadow mb-4">
        <div class="card-header bg-white fw-bold">1. Thông tin phân loại</div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Lớp:</label>
                    <select name="grade" class="form-select">
                        <option value="10">Lớp 10</option>
                        <option value="11">Lớp 11</option>
                        <option value="12">Lớp 12</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Định hướng:</label>
                    <select name="orientation" class="form-select">
                        <option value="chung">Chung</option>
                        <option value="ict">ICT</option>
                        <option value="cs">CS</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Chủ đề:</label>
                    <select name="topic_id" class="form-select" required>
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Năng lực (Chung cho cả câu):</label>
                    <select name="competency_id" class="form-select" required>
                        @foreach($competencies as $comp)
                            <option value="{{ $comp->id }}" title="{{ $comp->description }}">
                                {{ $comp->code }}: {{ Str::limit($comp->description, 25) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold">Loại câu hỏi:</label>
                    <select name="type" id="questionType" class="form-select" onchange="toggleForm()">
                        <option value="single_choice">Dạng 1: Trắc nghiệm</option>
                        <option value="true_false_group">Dạng 2: Đúng/Sai chùm</option>
                    </select>
                </div>

                <div class="col-md-4" id="mainLevelDiv">
                    <label class="form-label fw-bold">Mức độ nhận thức:</label>
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

    <div class="card shadow">
        <div class="card-header bg-white fw-bold">2. Nội dung chi tiết</div>
        <div class="card-body">
            
            <div id="formSingleChoice">
                <div class="mb-3">
                    <label class="form-label">Nội dung câu hỏi:</label>
                    <textarea name="content" class="form-control" rows="3"></textarea>
                </div>
                @for($i = 1; $i <= 4; $i++)
                    <div class="input-group mb-2">
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" type="radio" name="correct_answer" value="{{ $i }}" {{ $i==1 ? 'checked' : '' }}>
                        </div>
                        <input type="text" name="answers[{{ $i }}]" class="form-control" placeholder="Đáp án {{ $i }}" required>
                    </div>
                @endfor
            </div>

            <div id="formTrueFalse" style="display: none;">
                <div class="mb-3">
                    <label class="form-label fw-bold">Đoạn văn dẫn (Không có mức độ):</label>
                    <textarea id="tf_content" class="form-control" rows="3" placeholder="Nhập đoạn văn dẫn..."></textarea>
                </div>
                
                <label class="form-label fw-bold text-primary">4 Ý nhận định con (Chọn mức độ cho từng ý):</label>
                @for($i = 0; $i < 4; $i++)
                    <div class="card mb-3 bg-light border">
                        <div class="card-body p-2">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <input type="text" name="sub_questions[{{ $i }}][content]" class="form-control" placeholder="Nội dung ý {{ $i+1 }}...">
                                </div>
                                
                                <div class="col-md-3">
                                    <select name="sub_questions[{{ $i }}][cognitive_level_id]" class="form-select form-select-sm">
                                        <option value="">-- Mức độ --</option>
                                        @foreach($levels as $level)
                                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <select name="sub_questions[{{ $i }}][correct_option]" class="form-select form-select-sm">
                                        <option value="true">Là ĐÚNG</option>
                                        <option value="false">Là SAI</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary px-5">Lưu câu hỏi</button>
                <a href="{{ route('teacher.questions.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleForm() {
        var type = document.getElementById('questionType').value;
        var formSingle = document.getElementById('formSingleChoice');
        var formTF = document.getElementById('formTrueFalse');
        var mainLevelDiv = document.getElementById('mainLevelDiv');
        var mainLevelSelect = mainLevelDiv.querySelector('select');
        
        var mainContent = document.querySelector('textarea[name="content"]');

        // Reset trạng thái disable
        var inputsSingle = formSingle.querySelectorAll('input, textarea');
        var inputsTF = formTF.querySelectorAll('input, textarea, select');

        if (type === 'single_choice') {
            formSingle.style.display = 'block';
            formTF.style.display = 'none';
            mainLevelDiv.style.visibility = 'visible'; // Hiện mức độ chung
            mainLevelSelect.required = true;           // Bắt buộc nhập

            inputsSingle.forEach(el => el.disabled = false);
            inputsTF.forEach(el => el.disabled = true);
        } else {
            formSingle.style.display = 'none';
            formTF.style.display = 'block';
            mainLevelDiv.style.visibility = 'hidden';  // Ẩn mức độ chung
            mainLevelSelect.required = false;          // Không bắt buộc nhập

            inputsSingle.forEach(el => el.disabled = true);
            inputsTF.forEach(el => el.disabled = false);
        }
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        var type = document.getElementById('questionType').value;
        if (type === 'true_false_group') {
            var mainContent = document.querySelector('textarea[name="content"]');
            var tfContent = document.getElementById('tf_content');
            mainContent.value = tfContent.value;
            mainContent.disabled = false;
        }
    });
    
    toggleForm();
</script>
</body>
</html>