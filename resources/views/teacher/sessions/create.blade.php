<x-layouts.teacher title="Tổ chức kỳ thi mới">

    @push('styles')
    <style>
        /* --- STYLE HIỆN ĐẠI (CLEAN UI) --- */
        :root { --primary-color: #4f46e5; --text-secondary: #64748b; }

        /* Card Styles */
        .card-custom {
            border: none; border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02);
            /* Đã xóa h-100 để không bị khoảng trắng thừa */
        }
        
        /* Form Inputs */
        .form-label { font-weight: 600; color: #334155; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .form-control, .form-select {
            padding: 0.75rem 1rem; border-color: #e2e8f0; border-radius: 8px; font-size: 0.95rem;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        /* Upload Area */
        .upload-area {
            border: 2px dashed #cbd5e1; border-radius: 12px; padding: 1.5rem;
            text-align: center; background-color: #f8fafc; transition: all 0.2s; cursor: pointer;
            position: relative;
        }
        .upload-area:hover { border-color: var(--primary-color); background-color: #eef2ff; }
        .upload-icon { font-size: 2rem; color: #94a3b8; margin-bottom: 0.5rem; }
        
        /* Section Divider */
        .form-section-title {
            font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;
            color: #94a3b8; font-weight: 700; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;
        }

        /* Buttons */
        .btn-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border: none; color: white; padding: 0.75rem 1.5rem; font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); transition: all 0.2s;
        }
        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 6px 12px -1px rgba(79, 70, 229, 0.3); color: white; }
    </style>
    @endpush

    {{-- ĐÃ XÓA PHẦN HEADER/BREADCRUMB Ở ĐÂY --}}

    <form action="{{ route('teacher.sessions.store') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf
        <div class="row g-4">
            
            {{-- CỘT TRÁI: FORM NHẬP LIỆU CHÍNH (GỘP TẤT CẢ VÀO ĐÂY) --}}
            <div class="col-lg-9">
                <div class="card card-custom bg-white">
                    <div class="card-body p-5">
                        
                        {{-- PHẦN 1: THÔNG TIN CƠ BẢN --}}
                        <div class="mb-5">
                            <h6 class="form-section-title"><i class="bi bi-info-circle me-2"></i> Thông tin cơ bản</h6>
                            
                            <div class="mb-4">
                                <label class="form-label">Tên Ca thi / Kỳ thi <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control form-control-lg fw-bold text-dark" 
                                       placeholder="Nhập tên kỳ thi (VD: Thi cuối kỳ I - Lớp 12A)" required autofocus>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Chọn đề thi gốc <span class="text-danger">*</span></label>
                                <select name="exam_id" class="form-select" required>
                                    <option value="" selected disabled>-- Chọn đề thi từ thư viện --</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}">
                                            {{ $exam->title }} ({{ $exam->duration }} phút - {{ $exam->total_questions }} câu)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="start_at" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="end_at" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        {{-- PHẦN 2: BẢO MẬT & TRUY CẬP (ĐÃ CHUYỂN LÊN ĐÂY) --}}
                        <div>
                            <h6 class="form-section-title"><i class="bi bi-shield-lock me-2"></i> Bảo mật & Truy cập</h6>
                            
                            <div class="row g-4">
                                {{-- Mật khẩu --}}
                                <div class="col-md-6">
                                    <label class="form-label">Mật khẩu tham gia (Tùy chọn)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                        <input type="text" name="password" class="form-control border-start-0 ps-0" placeholder="Để trống nếu thi công khai">
                                    </div>
                                    <div class="form-text text-muted mt-2 small">
                                        Nếu đặt mật khẩu, học sinh phải nhập đúng mới được vào thi.
                                    </div>
                                </div>

                                {{-- Upload File --}}
                                <div class="col-md-6">
                                    <label class="form-label">Giới hạn danh sách (Excel)</label>
                                    <div class="upload-area">
                                        <div class="upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                                        <div class="fw-bold text-dark small mb-1">Chọn file .xlsx, .csv</div>
                                        <input type="file" name="student_file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" accept=".xlsx,.xls,.csv">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: NÚT HÀNH ĐỘNG (STICKY) --}}
            <div class="col-lg-3">
                <div class="card card-custom bg-white sticky-top" style="top: 20px; z-index: 1;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3">Tác vụ</h6>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-gradient rounded-3 py-2">
                                <i class="bi bi-check-circle-fill me-2"></i> Hoàn tất
                            </button>
                            <a href="{{ route('teacher.sessions.index') }}" class="btn btn-light text-secondary fw-bold rounded-3 py-2 border">
                                Hủy bỏ
                            </a>
                        </div>
                        
                        <div class="alert alert-info bg-info bg-opacity-10 border-0 rounded-3 mt-4 mb-0 small">
                            <div class="fw-bold mb-1"><i class="bi bi-lightbulb-fill me-1"></i> Lưu ý:</div>
                            Khi tạo xong, hệ thống sẽ cấp một <strong>Mã dự thi</strong>. Hãy gửi mã này cho học sinh.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

</x-layouts.teacher>