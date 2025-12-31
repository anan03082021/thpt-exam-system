<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả thi: {{ $exam->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-primary mb-0">Kết quả thi</h2>
                <h5 class="text-muted">{{ $exam->title }}</h5>
            </div>
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">Quay lại Dashboard</a>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6>Số lượng bài thi</h6>
                        <h3>{{ $attempts->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>Điểm cao nhất</h6>
                        <h3>{{ $attempts->max('total_score') ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h6>Điểm trung bình</h6>
                        <h3>{{ number_format($attempts->avg('total_score'), 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h6>Điểm thấp nhất</h6>
                        <h3>{{ $attempts->min('total_score') ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Danh sách học sinh nộp bài</h5>
            </div>
            <div class="card-body">
                @if($attempts->isEmpty())
                    <p class="text-center text-muted py-4">Chưa có học sinh nào làm bài thi này.</p>
                @else
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên học sinh</th>
                                <th>Email</th>
                                <th>Thời gian nộp</th>
                                <th>Điểm số</th>
                                <th>Xếp loại</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $index => $attempt)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $attempt->user->name }}</td>
                                    <td>{{ $attempt->user->email }}</td>
                                    <td>{{ $attempt->created_at->format('H:i d/m/Y') }}</td>
                                    <td class="fw-bold text-primary" style="font-size: 1.1em;">
                                        {{ $attempt->total_score }}
                                    </td>
                                    <td>
                                        @if($attempt->total_score >= 8)
                                            <span class="badge bg-success">Giỏi</span>
                                        @elseif($attempt->total_score >= 6.5)
                                            <span class="badge bg-info">Khá</span>
                                        @elseif($attempt->total_score >= 5)
                                            <span class="badge bg-warning text-dark">Trung bình</span>
                                        @else
                                            <span class="badge bg-danger">Yếu</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</body>
</html>