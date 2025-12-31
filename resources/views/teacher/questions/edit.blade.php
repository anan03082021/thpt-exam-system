<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa câu hỏi #{{ $question->id }}</title>
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

        <h3 class="mb-4 text-warning fw-bold">Chỉnh sửa câu hỏi #{{ $question->id }}</h3>
        
        <form action="{{ route('teacher.questions.update', $question->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card shadow mb-4">
                <div class="card-header bg-white fw-bold">1. Thông tin phân loại</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Lớp:</label>
                            <select name="grade" class="form-select">
                                <option value="10" {{ $question->grade == 10 ? 'selected' : '' }}>Lớp 10</option>
                                <option value="11" {{ $question->grade == 11 ? 'selected' : '' }}>Lớp 11</option>
                                <option value="12" {{ $question->grade == 12 ? 'selected' : '' }}>Lớp 12</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Định hướng:</label>
                            <select name="orientation" class="form-select">
                                <option value="chung" {{ $question->orientation == 'chung' ? 'selected' : '' }}>Chung</option>
                                <option value="ict" {{ $question->orientation == 'ict' ? 'selected' : '' }}>ICT</option>
                                <option value="cs" {{ $question->orientation == 'cs' ? 'selected' : '' }}>CS</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Chủ đề:</label>
                            <select name="topic_id" class="form-select">
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}" {{ $question->topic_id == $topic->id ? 'selected' : '' }}>
                                        {{ $topic->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Năng lực:</label>
                            <select name="competency_id" class="form-select">
                                @foreach($competencies as $comp)
                                    <option value="{{ $comp->id }}" {{ $question->competency_id == $comp->id ? 'selected' : '' }}>
                                        {{ $comp->code }}: {{ Str::limit($comp->description, 25) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Loại câu hỏi (Không thể đổi):</label>
                            <input type="text" class="form-control bg-secondary text-white" 
                                   value="{{ $question->type == 'single_choice' ? 'Trắc nghiệm' : 'Đúng/Sai chùm' }}" disabled>
                            <input type="hidden" name="type" value="{{ $question->type }}">
                        </div>

                        @if($question->type == 'single_choice')
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Mức độ nhận thức:</label>
                            <select name="cognitive_level_id" class="form-select" required>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" {{ $question->cognitive_level_id == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header bg-white fw-bold">2. Nội dung câu hỏi</div>
                <div class="card-body">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung / Đoạn văn dẫn:</label>
                        <textarea name="content" class="form-control" rows="3">{{ $question->content }}</textarea>
                    </div>

                    @if($question->type == 'single_choice')
                        <label class="form-label fw-bold">Các phương án:</label>
                        @foreach($question->answers as $index => $ans)
                            <div class="input-group mb-2">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer" value="{{ $index }}" {{ $ans->is_correct ? 'checked' : '' }}>
                                </div>
                                <input type="text" name="answers[{{ $index }}]" class="form-control" value="{{ $ans->content }}" required>
                            </div>
                        @endforeach
                    @endif

                    @if($question->type == 'true_false_group')
                        <label class="form-label fw-bold text-primary">4 Ý nhận định con (Sửa mức độ riêng từng ý):</label>
                        @foreach($question->children as $i => $child)
                            @php
                                // Tìm đáp án đúng hiện tại
                                $correctAnswer = $child->answers->where('is_correct', true)->first();
                                $isTrue = $correctAnswer && $correctAnswer->content == 'Đúng';
                            @endphp
                            <div class="card mb-3 bg-light border">
                                <div class="card-body p-2">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <input type="text" name="sub_questions[{{ $i }}][content]" class="form-control" value="{{ $child->content }}" placeholder="Nội dung ý...">
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <select name="sub_questions[{{ $i }}][cognitive_level_id]" class="form-select form-select-sm">
                                                @foreach($levels as $level)
                                                    <option value="{{ $level->id }}" {{ $child->cognitive_level_id == $level->id ? 'selected' : '' }}>
                                                        {{ $level->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <select name="sub_questions[{{ $i }}][correct_option]" class="form-select form-select-sm">
                                                <option value="true" {{ $isTrue ? 'selected' : '' }}>Là ĐÚNG</option>
                                                <option value="false" {{ !$isTrue ? 'selected' : '' }}>Là SAI</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-warning px-5 fw-bold">Cập nhật câu hỏi</button>
                        <a href="{{ route('teacher.questions.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>