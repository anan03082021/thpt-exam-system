<x-app-layout>
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }

        .profile-card {
            background: white; border-radius: 16px; border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); overflow: hidden;
            height: 100%; transition: all 0.3s ease;
        }
        
        .card-header-custom {
            padding: 1.25rem 1.5rem; background: #fff; border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; gap: 10px;
        }
        .header-title { font-weight: 700; color: #1e293b; margin: 0; font-size: 1.1rem; }
        .header-icon {
            width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
        }
        .bg-icon-blue { background: #eff6ff; color: #3b82f6; }
        .bg-icon-purple { background: #f5f3ff; color: #8b5cf6; }

        /* AVATAR UPLOAD STYLE */
        .avatar-container { position: relative; width: 140px; margin: 0 auto 1.5rem; }
        
        .avatar-wrapper {
            width: 140px; height: 140px; border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
            padding: 4px; box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
        }
        .avatar-img {
            width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid white; background: white;
        }

        /* Nút Upload Camera */
        .btn-upload-avatar {
            position: absolute; bottom: 5px; right: 5px;
            width: 40px; height: 40px; border-radius: 50%;
            background: #0f172a; color: white; border: 3px solid white;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            transition: transform 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn-upload-avatar:hover { transform: scale(1.1); background: #334155; }

        /* READONLY INPUT STYLE */
        .form-control-custom {
            width: 100%; padding: 0.75rem 1rem; border-radius: 12px;
            border: 1px solid #e2e8f0; font-size: 0.95rem; background: #fff;
        }
        /* Style cho ô bị khóa */
        .form-control-custom[readonly] {
            background-color: #f1f5f9; /* Màu xám */
            color: #64748b;
            cursor: not-allowed;
            border-color: #e2e8f0;
        }
        
        .btn-save {
            background: #0f172a; color: white; border: none; padding: 0.75rem 1.5rem;
            border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s;
        }
        .btn-save:hover { background: #334155; }
    </style>
    @endpush

    <div class="container py-5">
        
        {{-- THÔNG BÁO --}}
        @if (session('status') === 'avatar-updated')
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <span class="fw-bold">Ảnh đại diện đã được cập nhật!</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <div class="row g-4">
                {{-- CỘT TRÁI: AVATAR & THÔNG TIN CƠ BẢN --}}
                <div class="col-lg-4">
                    <div class="profile-card text-center p-5 h-100 d-flex flex-column justify-content-center">
                        
                        {{-- KHU VỰC AVATAR CÓ NÚT UPLOAD --}}
                        <div class="avatar-container">
                            <div class="avatar-wrapper">
                                @if($user->avatar)
                                    <img id="avatar-preview" src="{{ asset('storage/' . $user->avatar) }}" class="avatar-img" alt="Avatar">
                                @else
                                    <img id="avatar-preview" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff&size=200" class="avatar-img" alt="Avatar">
                                @endif
                            </div>
                            
                            {{-- Input file ẩn --}}
                            <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*" onchange="previewImage(event)">
                            
                            {{-- Nút Trigger --}}
                            <label for="avatar-input" class="btn-upload-avatar" title="Đổi ảnh đại diện">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                        </div>
                        
                        <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                        <div class="mb-4">
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                                {{ $user->role === 'student' ? 'Học sinh' : 'Giáo viên' }}
                            </span>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn-save">
                                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Lưu ảnh đại diện
                            </button>
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI: THÔNG TIN (READONLY) & ĐỔI PASS --}}
                <div class="col-lg-8">
                    <div class="d-flex flex-column gap-4">
                        
                        {{-- 1. FORM THÔNG TIN (KHÓA) --}}
                        <div class="profile-card">
                            <div class="card-header-custom">
                                <div class="header-icon bg-icon-blue"><i class="bi bi-person-lines-fill"></i></div>
                                <h5 class="header-title">Thông tin định danh</h5>
                            </div>
                            <div class="p-4">
                                <div class="alert alert-warning border-0 bg-orange-50 text-orange-700 d-flex align-items-center p-3 mb-4 rounded-3">
                                    <i class="bi bi-lock-fill me-2 fs-5"></i>
                                    <small>Thông tin Họ tên và Email được quản lý bởi nhà trường. Bạn không thể tự thay đổi.</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small text-uppercase fw-bold">Họ và tên</label>
                                        {{-- THÊM READONLY --}}
                                        <input type="text" class="form-control-custom" value="{{ $user->name }}" readonly>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small text-uppercase fw-bold">Email đăng nhập</label>
                                        {{-- THÊM READONLY --}}
                                        <input type="email" class="form-control-custom" value="{{ $user->email }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
        </form> {{-- Đóng form update avatar ở đây để tách biệt với form password --}}

                        {{-- 2. FORM ĐỔI MẬT KHẨU (RIÊNG BIỆT) --}}
                        <div class="profile-card">
                            <div class="card-header-custom">
                                <div class="header-icon bg-icon-purple"><i class="bi bi-shield-lock-fill"></i></div>
                                <h5 class="header-title">Đổi mật khẩu</h5>
                            </div>
                            <div class="p-4">
                                <form method="post" action="{{ route('password.update') }}">
                                    @csrf
                                    @method('put')

                                    <div class="mb-3">
                                        <label class="form-label">Mật khẩu hiện tại</label>
                                        <input type="password" name="current_password" class="form-control-custom" placeholder="••••••••">
                                        @error('current_password', 'updatePassword') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Mật khẩu mới</label>
                                            <input type="password" name="password" class="form-control-custom" placeholder="••••••••">
                                            @error('password', 'updatePassword') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Xác nhận mật khẩu</label>
                                            <input type="password" name="password_confirmation" class="form-control-custom" placeholder="••••••••">
                                        </div>
                                    </div>

                                    <div class="text-end mt-2">
                                        <button type="submit" class="btn-save" style="background: #4f46e5;">
                                            <i class="bi bi-key-fill me-2"></i> Cập nhật mật khẩu
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
    </div>

    {{-- Script xem trước ảnh --}}
    @push('scripts')
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('avatar-preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
    @endpush
</x-app-layout>