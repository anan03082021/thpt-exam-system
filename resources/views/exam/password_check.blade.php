<x-app-layout>
    @push('styles')
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-light: #e0e7ff;
            --text-color: #1f2937;
        }
        body { background-color: #f3f4f6; font-family: 'Inter', sans-serif; }

        /* Card trung tâm */
        .auth-card {
            background: white;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            overflow: hidden;
            max-width: 480px;
            width: 100%;
            margin: 80px auto; /* Canh giữa màn hình */
        }

        /* Dải màu trang trí */
        .auth-header-bar {
            height: 6px;
            background: linear-gradient(90deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
        }

        /* Icon ổ khóa */
        .lock-icon-wrapper {
            width: 80px; height: 80px;
            background: var(--primary-light);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            animation: pulse-soft 2s infinite;
        }

        @keyframes pulse-soft {
            0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.2); }
            70% { box-shadow: 0 0 0 10px rgba(79, 70, 229, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
        }

        /* Input mật khẩu */
        .form-control-lg {
            border-radius: 12px;
            padding: 15px;
            font-size: 1.2rem;
            text-align: center;
            letter-spacing: 5px;
            font-weight: 700;
            border: 2px solid #e5e7eb;
            background: #f9fafb;
            transition: all 0.2s;
        }

        .form-control-lg:focus {
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            letter-spacing: 2px; /* Hiệu ứng co chữ khi nhập */
        }
        
        .form-control-lg::placeholder {
            letter-spacing: normal;
            font-weight: 400;
            font-size: 1rem;
            color: #9ca3af;
        }

        /* Button */
        .btn-enter {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 10px;
            border: none;
            transition: all 0.2s;
        }
        .btn-enter:hover {
            background-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
        }

        .btn-back {
            background-color: white;
            color: #4b5563;
            font-weight: 600;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
        }
        .btn-back:hover { background-color: #f9fafb; color: #111827; }

        .exam-info-badge {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 25px;
            font-size: 0.95rem;
            color: #4b5563;
        }
    </style>
    @endpush

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="auth-card">
            {{-- Dải màu trang trí --}}
            <div class="auth-header-bar"></div>

            <div class="p-5">
                {{-- Header --}}
                <div class="text-center mb-4">
                    <div class="lock-icon-wrapper">
                        <i class="bi bi-shield-lock-fill fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">Xác thực quyền truy cập</h4>
                    <p class="text-muted small">Kỳ thi này yêu cầu mật khẩu bảo vệ</p>
                </div>

                {{-- Thông tin kỳ thi --}}
                <div class="exam-info-badge text-center border">
                    <small class="text-uppercase fw-bold text-muted d-block mb-1" style="font-size: 0.7rem;">Bạn đang truy cập</small>
                    <span class="fw-bold text-primary">{{ $session->title }}</span>
                </div>

                {{-- Form --}}
                <form action="{{ route('exam.join_password', $session->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4 position-relative">
                        <label for="password" class="form-label fw-bold text-secondary small text-uppercase">Mật khẩu ca thi</label>
                        <div class="position-relative">
                            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                                <i class="bi bi-key-fill fs-5"></i>
                            </span>
                            <input type="text" 
                                   name="password" 
                                   class="form-control form-control-lg ps-5" 
                                   placeholder="Nhập mật khẩu..." 
                                   required 
                                   autofocus 
                                   autocomplete="off">
                        </div>
                    </div>

                    {{-- Thông báo lỗi --}}
                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center mb-4 py-2" role="alert">
                            <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
                            <div class="small fw-bold">{{ session('error') }}</div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="row g-2">
                        <div class="col-5">
                            <a href="{{ route('dashboard') }}" class="btn btn-back w-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-arrow-left me-2"></i> Quay lại
                            </a>
                        </div>
                        <div class="col-7">
                            <button type="submit" class="btn btn-enter w-100 d-flex align-items-center justify-content-center">
                                Vào thi <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="bg-light py-3 text-center border-top">
                <small class="text-muted" style="font-size: 0.8rem;">
                    Vui lòng liên hệ giám thị nếu quên mật khẩu.
                </small>
            </div>
        </div>
    </div>
</x-app-layout>