<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo đề thi mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <h3 class="mb-4 text-primary fw-bold">Tạo đề thi & Chọn câu hỏi</h3>

        <div class="card shadow mb-4">
            <div class="card-header bg-white fw-bold">1. Bộ lọc câu hỏi</div>
            <div class="card-body bg-light">
                <form action="{{ route('teacher.exams.create') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Lớp</label>
                            <select name="grade" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="10" {{ request('grade') == '10' ? 'selected' : '' }}>Lớp 10</option>
                                <option value="11" {{ request('grade') == '11' ? 'selected' : '' }}>Lớp 11</option>
                                <option value="12" {{ request('grade') == '12' ? 'selected' : '' }}>Lớp 12</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Chủ đề</label>
                            <select name="topic_id" class="form-select">
                                <option value="">-- Tất cả --</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                                        {{ $topic->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Định hướng</label>
                            <select name="orientation" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="chung" {{ request('orientation') == 'chung' ? 'selected' : '' }}>Chung</option>
                                <option value="ict" {{ request('orientation') == 'ict' ? 'selected' : '' }}>ICT</option>
                                <option value="cs" {{ request('orientation') == 'cs' ? 'selected' : '' }}>CS</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Dạng câu hỏi</label>
                            <select name="type" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="single_choice" {{ request('type') == 'single_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                                <option value="true_false_group" {{ request('type') == 'true_false_group' ? 'selected' : '' }}>Đúng/Sai chùm</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Mức độ (Chỉ dạng 1)</label>
                            <select name="cognitive_level_id" class="form-select">
                                <option value="">-- Tất cả --</option>
                                @foreach($levels as $lv)
                                    <option value="{{ $lv->id }}" {{ request('cognitive_level_id') == $lv->id ? 'selected' : '' }}>
                                        {{ $lv->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Năng lực</label>
                            <select name="competency_id" class="form-select">
                                <option value="">-- Tất cả --</option>
                                @foreach($competencies as $comp)
                                    <option value="{{ $comp->id }}" {{ request('competency_id') == $comp->id ? 'selected' : '' }}>
                                        {{ $comp->code }}: {{ $comp->description }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 text-end mt-3">
                            <a href="{{ route('teacher.exams.create') }}" class="btn btn-secondary me-2">Đặt lại</a>
                            <button type="submit" class="btn btn-primary px-4">🔍 Lọc câu hỏi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <form action="{{ route('teacher.exams.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Danh sách câu hỏi ({{ $questions->total() }} kết quả)</span>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                                <label class="form-check-label" for="checkAll">Chọn tất cả trang này</label>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Nội dung câu hỏi</th>
                                        <th width="120">Phân loại</th>
                                        <th width="100">Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($questions as $q)
                                        <tr>
                                            <td class="text-center">
    <input class="form-check-input question-checkbox" 
           type="checkbox" 
           value="{{ $q->id }}" 
           onchange="toggleQuestion(this.value, this.checked)">
</td>
                                            <td>
                                                <div class="fw-bold text-truncate" style="max-width: 400px;">{{ $q->content }}</div>
                                                <small class="text-muted">
                                                    {{ $q->topic->name ?? 'Chưa có chủ đề' }} | 
                                                    Lớp {{ $q->grade }} | 
                                                    {{ strtoupper($q->orientation) }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $q->type == 'single_choice' ? 'bg-primary' : 'bg-warning text-dark' }}">
                                                    {{ $q->type == 'single_choice' ? 'Trắc nghiệm' : 'Đúng/Sai' }}
                                                </span>
                                                <br>
                                                @if($q->cognitiveLevel)
                                                    <span class="badge bg-info text-dark mt-1">{{ $q->cognitiveLevel->name }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-secondary">Xem</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">Không tìm thấy câu hỏi nào phù hợp.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white">
                            {{ $questions->links() }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow sticky-top" style="top: 20px;">
                        <div class="card-header bg-primary text-white fw-bold">
                            Thông tin đề thi
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Tên đề thi <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Ví dụ: Kiểm tra 15 phút..." required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Thời gian làm bài (phút) <span class="text-danger">*</span></label>
                                <input type="number" name="duration" class="form-control" value="45" min="5" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu đề (Tùy chọn)</label>
                                <input type="text" name="password" class="form-control" placeholder="Để trống nếu công khai">
                            </div>

                            <div class="alert alert-info">
    <strong>Đã chọn:</strong> <span id="countSelected">0</span> câu hỏi.
</div>

<input type="hidden" name="question_ids" id="finalQuestionIds">

<button type="button" onclick="submitExamForm()" class="btn btn-success w-100 fw-bold py-2">
    ✅ HOÀN TẤT TẠO ĐỀ
</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

<script>
    // 1. Khởi tạo danh sách ID từ LocalStorage (hoặc mảng rỗng nếu chưa có)
    let selectedQuestions = JSON.parse(localStorage.getItem('exam_cart')) || [];

    // 2. Hàm chạy ngay khi load trang để tích lại các ô đã chọn trước đó
    document.addEventListener("DOMContentLoaded", function() {
        updateUI();
        
        // Duyệt qua tất cả checkbox trên trang hiện tại
        document.querySelectorAll('.question-checkbox').forEach(cb => {
            // Nếu ID của checkbox nằm trong danh sách đã lưu -> Tích vào
            if (selectedQuestions.includes(cb.value)) {
                cb.checked = true;
            }
        });
    });

    // 3. Hàm xử lý khi bấm vào 1 checkbox
    function toggleQuestion(id, isChecked) {
        if (isChecked) {
            // Nếu chưa có thì thêm vào
            if (!selectedQuestions.includes(id)) {
                selectedQuestions.push(id);
            }
        } else {
            // Nếu bỏ tích thì xóa khỏi mảng
            selectedQuestions = selectedQuestions.filter(item => item !== id);
        }
        
        // Lưu lại vào LocalStorage và cập nhật giao diện số lượng
        saveToStorage();
    }

    // 4. Hàm xử lý nút "Chọn tất cả trang này"
    document.getElementById('checkAll').addEventListener('change', function() {
        let isChecked = this.checked;
        document.querySelectorAll('.question-checkbox').forEach(cb => {
            cb.checked = isChecked;
            toggleQuestion(cb.value, isChecked); // Gọi hàm xử lý từng cái
        });
    });

    // 5. Các hàm phụ trợ
    function saveToStorage() {
        localStorage.setItem('exam_cart', JSON.stringify(selectedQuestions));
        updateUI();
    }

    function updateUI() {
        document.getElementById('countSelected').innerText = selectedQuestions.length;
    }

    // 6. Hàm xử lý khi bấm nút "Hoàn tất tạo đề"
    function submitExamForm() {
        if (selectedQuestions.length === 0) {
            alert("Bạn chưa chọn câu hỏi nào!");
            return;
        }

        // Đổ dữ liệu từ mảng vào input ẩn (nối nhau bằng dấu phẩy)
        document.getElementById('finalQuestionIds').value = selectedQuestions.join(',');

        // Xóa Storage để lần tạo đề sau không bị nhớ lại đề này (Tùy chọn)
        localStorage.removeItem('exam_cart'); 

        // Submit form thủ công
        document.querySelector('form[action*="store"]').submit();
    }
</script>
</body>
</html>