<x-layouts.teacher title="Tổ chức kỳ thi mới">

    @push('styles')
    <style>
        /* Tông màu chủ đạo cho trang Ca thi (Indigo/Tím than) */
        .card-header-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
        }
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
        }
        .btn-indigo {
            background-color: #4f46e5;
            color: white;
            border: none;
        }
        .btn-indigo:hover {
            background-color: #4338ca;
            color: white;
        }
        
        /* Upload box */
        .upload-box {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            background-color: #f9fafb;
            transition: all 0.2s;
        }
        .upload-box:hover {
            border-color: #6366f1;
            background-color: #eef2ff;
        }
    </style>
    @endpush

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.sessions.index') }}" class="text-decoration-none text-muted">Ca thi</a></li>
            <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Tổ chức mới</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-3 overflow-hidden">
                
                {{-- Header Card --}}
                <div class="card-header card-header-custom py-3">
                    <h5 class="mb-0 fw-bold d-flex align-items-center">
                        <i class="bi bi-calendar-check-fill me-2 fs-5"></i> Thiết lập Ca thi / Kỳ thi
                    </h5>
                </div>

                <div class="card-body p-4 bg-white">
                    <form action="{{ route('teacher.sessions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- 1. Tên kỳ thi --}}
                        <div class="mb-4">
                            <label class="form-label">Tên Ca thi / Kỳ thi <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="title" 
                                   class="form-control form-control-lg bg-light border-0" 
                                   placeholder="Ví dụ: Thi cuối kỳ I - Lớp 12A1" 
                                   required 
                                   autofocus>
                            <div class="form-text text-muted">Tên hiển thị cho học sinh thấy trên Dashboard.</div>
                        </div>

                        {{-- 2. Chọn đề gốc --}}
                        <div class="mb-4">
                            <label class="form-label">Chọn đề thi gốc <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-file-earmark-text"></i></span>
                                <select name="exam_id" class="form-select bg-light border-0" required>
                                    <option value="" selected disabled>-- Chọn đề thi từ thư viện --</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}">
                                            {{ $exam->title }} ({{ $exam->duration }} phút - {{ $exam->total_questions }} câu)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-text text-muted">Đề thi này sẽ được sử dụng để trộn đề cho các thí sinh.</div>
                        </div>

                        <hr class="my-4 border-light">

                        {{-- 3. Thời gian tổ chức --}}
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock me-2 text-primary"></i>Thời gian tổ chức</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_at" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_at" class="form-control" required>
                            </div>
                        </div>

                        <hr class="my-4 border-light">

                        {{-- 4. Bảo mật & Danh sách thí sinh --}}
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock me-2 text-primary"></i>Bảo mật & Thí sinh</h6>
                        
                        {{-- Mật khẩu tham gia --}}
                        <div class="mb-4">
                            <label class="form-label">Mật khẩu tham gia (Tùy chọn)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-key"></i></span>
                                <input type="text" 
                                       name="password" 
                                       class="form-control bg-light border-0" 
                                       placeholder="Để trống nếu không cần mật khẩu">
                            </div>
                            <div class="form-text text-muted">
                                <i class="bi bi-info-circle"></i> Nếu đặt mật khẩu, bất kỳ ai có mật khẩu đều có thể vào thi (kể cả không có trong danh sách).
                            </div>
                        </div>

                        {{-- Upload danh sách --}}
                        <div class="mb-4">
                            <label class="form-label">Giới hạn danh sách thí sinh (Excel)</label>
                            <div class="upload-box position-relative">
                                <i class="bi bi-cloud-arrow-up text-primary fs-2 mb-2"></i>
                                <p class="mb-2 fw-bold text-dark">Kéo thả file Excel hoặc click để chọn</p>
                                <p class="small text-muted mb-0">Chỉ chấp nhận file .xlsx, .xls, .csv (Chứa cột Email)</p>
                                <input type="file" 
                                       name="student_file" 
                                       class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" 
                                       accept=".xlsx, .xls, .csv">
                            </div>
                            <div class="form-text text-muted mt-2">
                                Hệ thống sẽ tự động thêm những Email <strong>đã có tài khoản</strong> vào kỳ thi này.
                            </div>
                        </div>

                        <hr class="my-4 border-light">

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('teacher.dashboard') }}" class="btn btn-light text-secondary fw-bold px-4">
                                Hủy bỏ
                            </a>
                            <button type="submit" class="btn btn-indigo fw-bold px-4 shadow-sm">
                                <i class="bi bi-check-circle me-1"></i> Hoàn tất & Tổ chức thi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Side Note --}}
        <div class="col-lg-4 d-none d-lg-block">
            <div class="alert alert-light border shadow-sm rounded-3">
                <h6 class="alert-heading fw-bold text-dark"><i class="bi bi-lightbulb-fill me-2 text-warning"></i>Lưu ý quan trọng</h6>
                <ul class="small text-muted ps-3 mb-0">
                    <li class="mb-2"><strong>Thời gian:</strong> Học sinh chỉ có thể làm bài trong khoảng thời gian "Bắt đầu" đến "Kết thúc".</li>
                    <li class="mb-2"><strong>Đảo đề:</strong> Nếu đề gốc có bật chế độ "Đảo câu hỏi", mỗi học sinh sẽ nhận được một mã đề khác nhau.</li>
                    <li><strong>Kết quả:</strong> Điểm số sẽ được cập nhật ngay sau khi học sinh nộp bài.</li>
                </ul>
            </div>
        </div>
    </div>
</x-layouts.teacher>