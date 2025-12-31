<x-app-layout>
    {{-- 1. Tiêu đề Header (Hiển thị trên thanh xám) --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kết quả chi tiết') }}
        </h2>
    </x-slot>

    {{-- 2. Nhúng Bootstrap CSS (Chỉ dùng cho phần nội dung bên dưới để giữ style bạn muốn) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- 3. Phần nội dung chính (Đã được bọc trong khung Layout của hệ thống) --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white shadow-sm sm:rounded-lg">
            
            {{-- BẮT ĐẦU GIAO DIỆN CỦA BẠN --}}
            <div class="container py-5">
                <div class="card shadow mb-4 text-center">
                    <div class="card-body p-5">
                        <h1 class="display-4 {{ $score >= 5 ? 'text-success' : 'text-danger' }}">
                            {{-- Nếu có điểm thì hiện, nếu null thì hiện 0 --}}
Kết quả: {{ isset($score) && $score !== '' ? number_format($score, 2) : '0' }} điểm
                        </h1>
                        <hr>
                        
                        @if(count($suggestions) > 0)
                            <h5 class="text-start text-warning fw-bold">⚠️ Vấn đề cần cải thiện:</h5>
                            <ul class="list-group text-start mb-3">
                                @foreach($suggestions as $suggest)
                                    <li class="list-group-item">{{ $suggest }}</li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-success">Tuyệt vời! Bạn nắm vững kiến thức.</div>
                        @endif

                        {{-- Nút quay lại: Sử dụng Route của Laravel để đảm bảo logic đúng --}}
                        <a href="{{ route('exam.take', $attemptDetail->exam_session_id ?? 0) }}" class="btn btn-primary mt-3">
                            🔄 Làm lại bài thi
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary mt-3 ms-2">
                            🏠 Về trang chủ
                        </a>
                    </div>
                </div>

                <h3 class="mb-3 text-danger fw-bold">Chi tiết các câu trả lời sai:</h3>
                
                @php
                    // Lọc ra các câu sai từ danh sách trả lời
                    $wrongAnswers = $attemptDetail->attemptAnswers->where('is_correct', false);
                @endphp

                @if($wrongAnswers->count() > 0)
                    @foreach($wrongAnswers as $ans)
                        <div class="card mb-3 border-danger">
                            <div class="card-header bg-danger text-white d-flex justify-content-between">
                                <span>
                                    <strong>Chủ đề:</strong> {{ $ans->question->topic->name ?? 'Chung' }}
                                </span>
                                <span class="badge bg-white text-danger">Sai</span>
                            </div>
                            <div class="card-body">
                                {{-- HIỂN THỊ NỘI DUNG CÂU HỎI --}}
                                <h5 class="card-title text-decoration-underline">Câu hỏi:</h5>
                                
                                @if($ans->question->type == 'true_false_item' && $ans->question->parent)
                                    {{-- Nếu là câu chùm D2: Hiển thị đoạn văn cha trước --}}
                                    <div class="alert alert-secondary fst-italic p-3 mb-2">
                                        <small class="fw-bold">Đoạn văn dẫn:</small><br>
                                        {{-- Dùng nl2br để giữ xuống dòng nếu có --}}
                                        {!! nl2br(e($ans->question->parent->content)) !!}
                                    </div>
                                    <p class="fw-bold fs-5">{{ $ans->question->content }}</p>
                                @else
                                    {{-- Câu đơn bình thường --}}
                                    <p class="fw-bold fs-5">{{ $ans->question->content }}</p>
                                @endif

                                <hr>

                                {{-- SO SÁNH ĐÁP ÁN --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="text-danger mb-1">❌ <strong>Bạn chọn:</strong></p>
                                        <div class="p-2 border border-danger bg-light rounded text-danger fw-bold">
                                            {{ $ans->selectedAnswer->content ?? 'Không chọn đáp án' }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-success mb-1">✅ <strong>Đáp án đúng là:</strong></p>
                                        <div class="p-2 border border-success bg-light rounded text-success fw-bold">
                                            {{-- Tìm đáp án đúng trong DB --}}
                                            @foreach($ans->question->answers as $correctOpt)
                                                @if($correctOpt->is_correct)
                                                    {{ $correctOpt->content }}
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-success text-center py-5">
                        <h4>🎉 Chúc mừng! Bạn không làm sai câu nào.</h4>
                    </div>
                @endif
            </div>
            {{-- KẾT THÚC GIAO DIỆN CỦA BẠN --}}
            
        </div>
    </div>
</x-app-layout>