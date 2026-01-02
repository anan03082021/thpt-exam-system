<x-layouts.teacher title="Tổ chức kỳ thi mới">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-primary">📝 Tổ chức Kỳ thi mới</h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('teacher.sessions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Tên kỳ thi --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên kỳ thi <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        {{-- Chọn đề thi --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn đề thi gốc <span class="text-danger">*</span></label>
                            <select name="exam_id" class="form-select" required>
                                <option value="">-- Chọn đề thi --</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->title }} ({{ $exam->duration }} phút)</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- [MỚI] Mật khẩu tham gia --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mật khẩu Ca thi (Tùy chọn)</label>
                            <input type="text" name="password" class="form-control" placeholder="VD: 123456 (Để trống nếu chỉ cho phép danh sách Email)">
                            <div class="form-text text-muted">
                                Nếu nhập mật khẩu: Học sinh <strong>không có trong danh sách</strong> vẫn có thể vào thi nếu biết mật khẩu này.
                            </div>
                        </div>

                        {{-- Thời gian --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_at" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_at" class="form-control" required>
                            </div>
                        </div>

                        {{-- [SỬA] Upload file Excel --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Danh sách Email cho phép (Excel)</label>
                            <input type="file" name="student_file" class="form-control" accept=".xlsx, .xls, .csv">
                            <div class="form-text text-muted">
                                <strong>Lưu ý:</strong> File chỉ cần 1 cột chứa <strong>Email</strong>. 
                                Hệ thống chỉ thêm những Email <strong>đã có tài khoản</strong>. Email chưa đăng ký sẽ bị bỏ qua.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 border-top pt-3">
                            <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary fw-bold">Tạo kỳ thi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.teacher>