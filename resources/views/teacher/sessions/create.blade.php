<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tổ chức kỳ thi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h3 class="mb-4 text-primary fw-bold">Tổ chức kỳ thi mới</h3>
        
        <form action="{{ route('teacher.sessions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="card shadow">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên kỳ thi (VD: Thi HK1 - Lớp 10A1):</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn đề thi:</label>
                        <select name="exam_id" class="form-select" required>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->title }} ({{ $exam->duration }} phút)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thời gian bắt đầu:</label>
                            <input type="datetime-local" name="start_at" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thời gian kết thúc:</label>
                            <input type="datetime-local" name="end_at" class="form-control" required>
                        </div>
                        <div class="form-text">Học sinh chỉ có thể vào làm bài trong khoảng thời gian này.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Danh sách học sinh (Excel):</label>
                        <input type="file" name="student_file" class="form-control" accept=".xlsx, .xls, .csv" required>
                        <div class="form-text text-muted">
                            File Excel cần có 2 cột theo thứ tự: <strong>Họ tên</strong> | <strong>Email</strong>.
                            <br>
                            <a href="#" onclick="alert('Hãy tạo file Excel có cột A là Tên, cột B là Email')">Tải file mẫu (Demo)</a>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-5">Tạo kỳ thi</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>