<x-app-layout>
    
    @push('styles')
    <style>
        /* Banner Gradient Xanh Lá */
        .practice-banner {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            color: white;
            border-radius: 20px;
            padding: 3rem 2.5rem;
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(25, 135, 84, 0.2);
        }

        /* Card Đề thi */
        .practice-card {
            border: none;
            border-radius: 16px;
            background: #fff;
            transition: all 0.3s ease;
            height: 100%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
            border-bottom: 4px solid #e9ecef; /* Viền dưới mặc định */
        }

        .practice-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border-bottom-color: #198754; /* Đổi màu viền khi hover */
        }

        /* Badge chủ đề */
        .topic-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 8px;
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }

        /* Nút Luyện ngay */
        .btn-practice {
            border-radius: 10px;
            font-weight: 700;
            padding: 10px 0;
            transition: all 0.2s;
        }
        .btn-practice:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
        }
    </style>
    @endpush

    {{-- 1. BANNER GIỚI THIỆU --}}
    <div class="practice-banner">
        <div class="row align-items-center position-relative z-1">
            <div class="col-lg-8">
                <h1 class="fw-bold mb-2">Kho đề thi mẫu 📚</h1>
                <p class="mb-0 fs-5 opacity-90">Rèn luyện kỹ năng với ngân hàng đề thi đa dạng chủ đề.</p>
            </div>
            <div class="col-lg-4 text-end d-none d-lg-block">
                <i class="bi bi-journal-check" style="font-size: 6rem; opacity: 0.2;"></i>
            </div>
        </div>
        {{-- Họa tiết nền --}}
        <div class="position-absolute bg-white opacity-10 rounded-circle" style="width: 200px; height: 200px; bottom: -50px; right: -50px;"></div>
    </div>

    {{-- 2. DANH SÁCH ĐỀ THI --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center">
            <span class="bg-success bg-opacity-10 text-success rounded-3 p-2 me-3">
                <i class="bi bi-lightning-charge-fill"></i>
            </span>
            Đề luyện tập có sẵn
        </h4>
        <span class="badge bg-white text-secondary border px-3 py-2 rounded-pill shadow-sm">
            {{ isset($practiceExams) ? $practiceExams->count() : 0 }} đề thi
        </span>
    </div>

    @if(isset($practiceExams) && $practiceExams->count() > 0)
        <div class="row g-4">
            @foreach($practiceExams as $exam)
                <div class="col-md-6 col-lg-4">
                    <div class="card practice-card">
                        <div class="card-body p-4 d-flex flex-column">
                            
                            {{-- Header: Topic Badge --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="topic-badge text-truncate" style="max-width: 100%;">
                                    <i class="bi bi-bookmarks-fill me-1 text-success"></i> 
                                    {{ $exam->topic->name ?? 'Tổng hợp' }}
                                </span>
                            </div>

                            {{-- Title --}}
                            <h5 class="card-title fw-bold text-dark mb-2 text-truncate" title="{{ $exam->title }}">
                                {{ $exam->title }}
                            </h5>
                            
                            {{-- Info --}}
                            <div class="text-secondary small mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-stopwatch text-success me-2"></i>
                                    Thời gian: <span class="fw-bold text-dark ms-1">{{ $exam->duration }} phút</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Trạng thái: <span class="text-success fw-bold ms-1">Mở tự do</span>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <hr class="border-light my-3">

                            {{-- Button --}}
                            <div class="mt-auto">
                                <a href="{{ route('exam.practice', $exam->id) }}" 
                                   class="btn btn-success w-100 btn-practice text-white">
                                    <i class="bi bi-play-fill me-1 fs-5 align-middle"></i> LUYỆN NGAY
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- EMPTY STATE --}}
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
            <div class="mb-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="bi bi-inbox text-success" style="font-size: 2.5rem; opacity: 0.5;"></i>
                </div>
            </div>
            <h5 class="fw-bold text-secondary">Chưa có đề thi mẫu</h5>
            <p class="text-muted mb-0">Hệ thống đang cập nhật thêm đề thi. Vui lòng quay lại sau.</p>
        </div>
    @endif

</x-app-layout>