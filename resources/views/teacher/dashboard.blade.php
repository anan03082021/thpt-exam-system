{{-- Lưu ý: Dòng này sẽ tự động tìm file resources/views/layouts/teacher.blade.php nhờ Bước 1 --}}
<x-layouts.teacher title="Dashboard Giáo viên">
    
    {{-- Phần thống kê --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Ngân hàng câu hỏi</h5>
                    <p class="card-text display-6 fw-bold">{{ $questionCount }} câu</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-info mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Đề thi đã tạo</h5>
                    <p class="card-text display-6 fw-bold">{{ $examCount }} đề</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách đề thi --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">Danh sách đề thi của tôi</h5>
            <a href="{{ route('teacher.exams.create') }}" class="btn btn-primary btn-sm">+ Tạo đề thi mới</a>
        </div>
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên đề thi</th>
                        <th>Số câu</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myExams as $exam)
                        <tr>
                            <td>#{{ $exam->id }}</td>
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
                                <a href="{{ route('teacher.exams.results', $exam->id) }}" class="btn btn-sm btn-info text-white">Kết quả</a>
                                <button class="btn btn-sm btn-outline-primary">Sửa</button>
                                <button class="btn btn-sm btn-outline-danger">Xóa</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($myExams->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted mb-0">Bạn chưa tạo đề thi nào.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.teacher>