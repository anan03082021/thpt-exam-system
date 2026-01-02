<x-layouts.teacher title="Sửa câu hỏi #{{ $question->id }}">

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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-warning fw-bold mb-0">✏️ Chỉnh sửa câu hỏi #{{ $question->id }}</h3>
        <a href="{{ route('teacher.questions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('teacher.questions.update', $question->id) }}" method="POST">
        @csrf
        @method('PUT')
        
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
                            <option value="10" {{ $question->grade == 10 ? 'selected' : '' }}>Lớp 10</option>
                            <option value="11" {{ $question->grade == 11 ? 'selected' : '' }}>Lớp 11</option>
                            <option value="12" {{ $question->grade == 12 ? 'selected' : '' }}>Lớp 12</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Định hướng <span class="text-danger">*</span></label>
                        <select name="orientation" class="form-select">
                            <option value="chung" {{ $question->orientation == 'chung' ? 'selected' : '' }}>Chung</option>
                            <option value="ict" {{ $question->orientation == 'ict' ? 'selected' : '' }}>ICT</option>
                            <option value="cs" {{ $question->orientation == 'cs' ? 'selected' : '' }}>CS</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Chủ đề <span class="text-danger">*</span></label>
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
                        <label class="form-label fw-bold">Năng lực</label>
                        <select name="competency_id" class="form-select">
                            @foreach($competencies as $comp)
                                <option value="{{ $comp->id }}" {{ $question->competency_id == $comp->id ? 'selected' : '' }} title="{{ $comp->description }}">
                                    {{ $comp->code }}: {{ Str::limit($comp->description, 30) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Loại câu hỏi</label>
                        <input type="text" class="form-control bg-secondary bg-opacity-10" 
                               value="{{ $question->type == 'single_choice' ? 'Trắc nghiệm' : 'Đúng/Sai' }}" disabled readonly>
                        {{-- Input ẩn để giữ giá trị type --}}
                        <input type="hidden" name="type" value="{{ $question->type }}">
                    </div>

                    {{-- Chỉ hiện mức độ chung nếu là Trắc nghiệm --}}
                    @if($question->type == 'single_choice')
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Mức độ nhận thức <span class="text-danger">*</span></label>
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

        {{-- CARD 2: NỘI DUNG CHI TIẾT --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3 border-bottom">
                <i class="bi bi-pencil-square"></i> 2. Nội dung chi tiết
            </div>
            <div class="card-body">
                
                {{-- NỘI DUNG CHUNG / ĐOẠN VĂN DẪN --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">
                        {{ $question->type == 'single_choice' ? 'Nội dung câu hỏi' : 'Đoạn văn dẫn' }} 
                        <span class="text-danger">*</span>
                    </label>
                    <textarea name="content" class="form-control" rows="3" required>{{ $question->content }}</textarea>
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
                                // Logic xác định đúng sai hiện tại
                                $correctAnswer = $child->answers->where('is_correct', true)->first();
                                $isTrue = $correctAnswer && $correctAnswer->content == 'Đúng';
                            @endphp
                            
                            <div class="col-md-12">
                                <div class="card mb-3 bg-light border-0">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center g-2">
                                            <div class="col-md-1 text-center fw-bold text-secondary">
                                                Ý {{ $i+1 }}
                                            </div>
                                            
                                            {{-- Nội dung ý --}}
                                            <div class="col-md-6">
                                                <input type="text" name="sub_questions[{{ $i }}][content]" class="form-control" value="{{ $child->content }}" placeholder="Nội dung ý..." required>
                                            </div>
                                            
                                            {{-- Mức độ riêng từng ý --}}
                                            <div class="col-md-3">
                                                <select name="sub_questions[{{ $i }}][cognitive_level_id]" class="form-select form-select-sm">
                                                    @foreach($levels as $level)
                                                        <option value="{{ $level->id }}" {{ $child->cognitive_level_id == $level->id ? 'selected' : '' }}>
                                                            {{ $level->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Đúng/Sai --}}
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

</x-layouts.teacher>