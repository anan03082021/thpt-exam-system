<x-layouts.shared title="Chỉnh sửa Ca thi">

    <div class="container-fluid p-0" style="max-width: 800px;">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('teacher.sessions.index') }}" class="text-decoration-none text-muted">Ca thi</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Chỉnh sửa #{{ $session->id }}</li>
            </ol>
        </nav>

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-pencil-square me-2 text-primary"></i> Cập nhật thông tin Ca thi
                </h5>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('teacher.sessions.update', $session->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Bắt buộc để Update --}}

                    <div class="row g-4">
                        {{-- Tên ca thi --}}
                        <div class="col-12">
                            <label class="form-label fw-bold">Tên ca thi <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg" 
                                   value="{{ $session->title }}" required placeholder="VD: Thi học kỳ 1 - Lớp 10A1">
                        </div>

                        {{-- Chọn đề thi --}}
                        <div class="col-12">
                            <label class="form-label fw-bold">Đề thi gốc <span class="text-danger">*</span></label>
                            <select name="exam_id" class="form-select">
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ $session->exam_id == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->title }} ({{ $exam->duration }} phút) - {{ $exam->total_questions }} câu
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-warning"><i class="bi bi-exclamation-triangle"></i> Lưu ý: Thay đổi đề thi khi đã có học sinh làm bài có thể gây lỗi dữ liệu.</div>
                        </div>

                        {{-- Thời gian bắt đầu --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Bắt đầu lúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_at" class="form-control" 
                                   value="{{ \Carbon\Carbon::parse($session->start_at)->format('Y-m-d\TH:i') }}" required>
                        </div>

                        {{-- Thời gian kết thúc --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kết thúc lúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_at" class="form-control" 
                                   value="{{ \Carbon\Carbon::parse($session->end_at)->format('Y-m-d\TH:i') }}" required>
                        </div>

                        {{-- Mật khẩu --}}
                        <div class="col-12">
                            <label class="form-label fw-bold">Mật khẩu ca thi (Tùy chọn)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                <input type="text" name="password" class="form-control" 
                                       value="{{ $session->password }}" placeholder="Để trống nếu muốn công khai">
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="col-12 mt-4 d-flex justify-content-between">
                            <a href="{{ route('teacher.sessions.index') }}" class="btn btn-light border px-4">Quay lại</a>
                            <button type="submit" class="btn btn-primary fw-bold px-5 shadow-sm">
                                <i class="bi bi-save me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-layouts.shared>