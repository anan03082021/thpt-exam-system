<x-layouts.teacher title="Ngân hàng câu hỏi">

    {{-- Hiển thị thông báo thành công --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        {{-- Header của Card: Chứa Tiêu đề và Nút thêm mới --}}
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">📚 Ngân hàng câu hỏi</h5>
            <div>
                {{-- Nút về Dashboard không cần thiết nữa vì đã có trên Menu, nhưng tôi vẫn giữ lại nếu bạn thích --}}
                {{-- <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary btn-sm me-2">Dashboard</a> --}}
                
                <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary btn-sm">
                    + Thêm câu hỏi mới
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">ID</th>
                            <th style="width: 40%">Nội dung</th>
                            <th style="width: 15%">Loại</th>
                            <th style="width: 15%">Chủ đề</th>
                            <th style="width: 10%">Ngày tạo</th>
                            <th style="width: 15%" class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $q)
                            <tr>
                                <td class="fw-bold text-muted">#{{ $q->id }}</td>
                                <td>
                                    <div class="text-truncate" style="max-width: 400px;" title="{{ $q->content }}">
                                        {{ Str::limit($q->content, 80) }}
                                    </div>
                                </td>
                                <td>
                                    @if($q->type == 'single_choice')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">Trắc nghiệm</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Đúng/Sai</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $q->topic->name ?? 'Chưa phân loại' }}</span>
                                </td>
                                <td class="text-muted small">
                                    {{ $q->created_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('teacher.questions.edit', $q->id) }}" class="btn btn-sm btn-outline-primary">
                                            Sửa
                                        </a>
                                        
                                        <form action="{{ route('teacher.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa câu này? Hành động này không thể hoàn tác.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            @if($questions->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $questions->links() }}
                </div>
            @endif

            {{-- Hiển thị khi không có dữ liệu --}}
            @if($questions->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted mb-3">Chưa có câu hỏi nào trong ngân hàng dữ liệu.</p>
                    <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary">Tạo câu hỏi đầu tiên</a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.teacher>