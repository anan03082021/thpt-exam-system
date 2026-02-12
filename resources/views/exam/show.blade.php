<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>{{ $exam->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4">{{ $exam->title }}</h1>
        
        <form action="{{ url('/exam/'.$exam->id.'/submit') }}" method="POST">
            @csrf
            
            @foreach($exam->questions as $index => $question)
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Câu {{ $index + 1 }}:</h5>
                        
                        {{-- XỬ LÝ DẠNG 1: TRẮC NGHIỆM ĐƠN --}}
                        @if($question->type == 'single_choice')
                            <p class="card-text fw-bold">{{ $question->content }}</p>
                            <div class="ms-3">
                                @foreach($question->answers as $ans)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               id="ans_{{ $ans->id }}" 
                                               value="{{ $ans->id }}">
                                        <label class="form-check-label" for="ans_{{ $ans->id }}">
                                            {{ $ans->content }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                        {{-- XỬ LÝ DẠNG 2: ĐÚNG/SAI --}}
                        @elseif($question->type == 'true_false_group')
                            <div class="alert alert-info">
                                <strong>Đọc đoạn văn sau và trả lời các ý bên dưới:</strong><br>
                                {{ $question->content }}
                            </div>
                            
                            {{-- Lặp qua 4 ý con (a,b,c,d) --}}
                            @foreach($question->children as $child)
                                <div class="mb-3 border-bottom pb-2">
                                    <p class="mb-1">{{ $child->content }}</p>
                                    <div class="d-flex gap-4">
                                        @foreach($child->answers as $ans)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" 
                                                       name="answers[{{ $child->id }}]" 
                                                       id="ans_{{ $ans->id }}" 
                                                       value="{{ $ans->id }}">
                                                <label class="form-check-label" for="ans_{{ $ans->id }}">
                                                    {{ $ans->content }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">Nộp bài thi</button>
            </div>
        </form>
    </div>
</body>
</html>