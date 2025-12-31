<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Ngân hàng câu hỏi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between mb-3">
            <h3>Ngân hàng câu hỏi</h3>
            <div>
                <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary">+ Thêm câu hỏi mới</a>
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">Về Dashboard</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nội dung</th>
                            <th>Loại</th>
                            <th>Chủ đề</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $q)
                            <tr>
                                <td>{{ $q->id }}</td>
                                <td>{{ Str::limit($q->content, 100) }}</td>
                                <td>
                                    @if($q->type == 'single_choice')
                                        <span class="badge bg-info text-dark">Trắc nghiệm</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Đúng/Sai chùm</span>
                                    @endif
                                </td>
                                <td>{{ $q->topic->name ?? 'N/A' }}</td>
                                <td>{{ $q->created_at->format('d/m/Y') }}</td>
                                <td>
    <div class="d-flex gap-2">
        <a href="{{ route('teacher.questions.edit', $q->id) }}" class="btn btn-sm btn-warning">Sửa</a>
        
        <form action="{{ route('teacher.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa câu này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
        </form>
    </div>
</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $questions->links() }} {{-- Phân trang --}}
            </div>
        </div>
    </div>
</body>
</html>