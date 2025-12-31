<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Giáo Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Hệ thống thi THPT - Dành cho Giáo viên</a>
            <div class="d-flex gap-3">
                 <span class="text-white align-self-center">Xin chào, {{ Auth::user()->name }}</span>
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light">Đăng xuất</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Ngân hàng câu hỏi</h5>
                        <p class="card-text display-6">{{ $questionCount }} câu</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Đề thi đã tạo</h5>
                        <p class="card-text display-6">{{ $examCount }} đề</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách đề thi của tôi</h5>
                <a href="{{ route('teacher.exams.create') }}" class="btn btn-primary btn-sm">+ Tạo đề thi mới</a>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đề thi</th>
                            <th>Số câu hỏi</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myExams as $exam)
                            <tr>
                                <td>{{ $exam->id }}</td>
                                <td>{{ $exam->title }}</td>
                                <td>{{ $exam->total_questions }}</td>
                                <td>{{ $exam->duration }} phút</td>
                                <td>
                                    @if($exam->status == 'published')
                                        <span class="badge bg-success">Đang mở</span>
                                    @else
                                        <span class="badge bg-secondary">Nháp</span>
                                    @endif
                                </td>
                                <td>
    <a href="{{ route('teacher.exams.results', $exam->id) }}" class="btn btn-sm btn-info text-white">Xem kết quả</a> <button class="btn btn-sm btn-outline-primary">Sửa</button>
    <button class="btn btn-sm btn-outline-danger">Xóa</button>
</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($myExams->isEmpty())
                    <p class="text-center text-muted mt-3">Bạn chưa tạo đề thi nào.</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>