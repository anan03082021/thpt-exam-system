<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Làm bài: {{ $session->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sticky-timer {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .question-card:hover {
            border-color: #0d6efd;
        }
    </style>
</head>
<body class="bg-light user-select-none"> <div class="sticky-timer py-3 mb-4 border-bottom">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">{{ $session->title }}</h5>
                <small class="text-muted">Học sinh: {{ Auth::user()->name }}</small>
            </div>
            <div class="text-end">
                <div class="text-danger fw-bold fs-4" id="countdown">00:00:00</div>
                <small class="text-muted">Thời gian còn lại</small>
            </div>
            <button onclick="confirmSubmit()" class="btn btn-primary fw-bold px-4">
                Nộp bài
            </button>
        </div>
    </div>

    <div class="container pb-5">
        <form id="examForm" action="{{ route('exam.submit', $session->id) }}" method="POST">
            @csrf

            <input type="hidden" name="exam_id_hidden" value="{{ $exam->id }}">
            
            @foreach($exam->questions as $index => $question)
                <div class="card shadow-sm mb-4 question-card" id="q_{{ $question->id }}">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <span class="badge bg-secondary me-2" style="height: fit-content;">Câu {{ $index + 1 }}</span>
                            <div class="flex-grow-1 fw-bold">
                                {!! nl2br(e($question->content)) !!}
                            </div>
                        </div>

                        @if($question->type == 'single_choice')
                            <div class="ms-4">
                                @foreach($question->answers as $ans)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input border-dark" 
                                               type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               id="ans_{{ $ans->id }}" 
                                               value="{{ $ans->id }}">
                                        <label class="form-check-label" for="ans_{{ $ans->id }}">
                                            {{ $ans->content }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                        @elseif($question->type == 'true_false_group')
                            <div class="ms-4">
                                <table class="table table-bordered table-sm mt-3 bg-white">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>Ý nhận định</th>
                                            <th width="10%">Đúng</th>
                                            <th width="10%">Sai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($question->children as $child)
                                            @php
                                                // Tìm ID của đáp án Đúng và Sai trong Database để gán value
                                                $trueAns = $child->answers->firstWhere('content', 'Đúng');
                                                $falseAns = $child->answers->firstWhere('content', 'Sai');
                                            @endphp
                                            <tr>
                                                <td class="align-middle px-3">{{ $child->content }}</td>
                                                <td class="text-center align-middle">
                                                    @if($trueAns)
                                                        <input class="form-check-input border-dark" 
                                                               type="radio" 
                                                               name="answers[{{ $child->id }}]" 
                                                               value="{{ $trueAns->id }}">
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if($falseAns)
                                                        <input class="form-check-input border-dark" 
                                                               type="radio" 
                                                               name="answers[{{ $child->id }}]" 
                                                               value="{{ $falseAns->id }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="text-center mt-5">
                <button type="button" onclick="confirmSubmit()" class="btn btn-primary btn-lg px-5">
                    Hoàn thành & Nộp bài
                </button>
            </div>
        </form>
    </div>

<script>
    // 1. Cấu hình thời gian kết thúc (ĐÃ SỬA LỖI TẠI ĐÂY)
    // Sử dụng \Carbon\Carbon::parse() để đảm bảo xử lý được cả chuỗi và đối tượng ngày tháng
    var endTime = new Date("{{ \Carbon\Carbon::parse($session->end_at)->toIso8601String() }}").getTime();

    // 2. Hàm đếm ngược
    var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = endTime - now;

        // Tính toán giờ, phút, giây
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Hiển thị (Thêm logic kiểm tra null để tránh lỗi nếu element chưa load)
        var countdownElement = document.getElementById("countdown");
        if (countdownElement) {
            countdownElement.innerHTML = 
                (hours < 10 ? "0" + hours : hours) + ":" + 
                (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                (seconds < 10 ? "0" + seconds : seconds);
        }

        // Nếu hết giờ
        if (distance < 0) {
            clearInterval(x);
            if (countdownElement) countdownElement.innerHTML = "HẾT GIỜ";
            alert("Đã hết thời gian làm bài. Hệ thống sẽ tự động nộp bài!");
            submitExamForce();
        }
    }, 1000);

    // 3. Xử lý nộp bài
    function confirmSubmit() {
        if (confirm("Bạn có chắc chắn muốn nộp bài? Hành động này không thể hoàn tác.")) {
            submitExamForce();
        }
    }

    function submitExamForce() {
        // Tắt cảnh báo rời trang trước khi submit để tránh trình duyệt chặn
        window.onbeforeunload = null; 
        document.getElementById("examForm").submit();
    }

    // 4. Cảnh báo khi reload hoặc thoát trang
    window.onbeforeunload = function() {
        return "Bạn có chắc muốn thoát? Bài làm của bạn có thể bị mất.";
    };

    // 5. Chặn chuột phải
    document.addEventListener('contextmenu', event => event.preventDefault());
</script>
</body>
</html>