<x-layouts.teacher title="Tạo đề thi & Chọn câu hỏi">

    @push('styles')
    <style>
        /* Tông màu Indigo chủ đạo */
        .card-header-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
        }
        
        /* Filter Box */
        .filter-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }

        /* Checkbox to */
        .question-checkbox {
            width: 1.2em;
            height: 1.2em;
            cursor: pointer;
            border-color: #cbd5e1;
        }
        .question-checkbox:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        /* Sticky Sidebar */
        .sticky-sidebar {
            top: 100px; /* Cách top để không bị che bởi navbar */
            z-index: 99;
        }

        /* Badges */
        .badge-type-sc { background-color: #0ea5e9; } /* Single Choice - Xanh trời */
        .badge-type-tf { background-color: #f59e0b; } /* True False - Cam */
    </style>
    @endpush

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}" class="text-decoration-none text-muted">Đề thi</a></li>
            <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Tạo đề mới</li>
        </ol>
    </nav>

    <div class="container-fluid p-0">
        
        {{-- 1. BỘ LỌC CÂU HỎI --}}
        <div class="card shadow-sm mb-4 border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white fw-bold border-bottom py-3">
                <i class="bi bi-funnel-fill text-primary me-2"></i> Bộ lọc câu hỏi
            </div>
            <div class="card-body filter-box m-3">
                <form action="{{ route('teacher.exams.create') }}" method="GET">
                    <div class="row g-3">
                        {{-- Hàng 1 --}}
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Lớp</label>
                            <select name="grade" class="form-select form-select-sm border-0 shadow-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="10" {{ request('grade') == '10' ? 'selected' : '' }}>Lớp 10</option>
                                <option value="11" {{ request('grade') == '11' ? 'selected' : '' }}>Lớp 11</option>
                                <option value="12" {{ request('grade') == '12' ? 'selected' : '' }}>Lớp 12</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">Chủ đề</label>
                            <select name="topic_id" class="form-select form-select-sm border-0 shadow-sm">
                                <option value="">-- Tất cả --</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                                        {{ $topic->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Định hướng</label>
                            <select name="orientation" class="form-select form-select-sm border-0 shadow-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="chung" {{ request('orientation') == 'chung' ? 'selected' : '' }}>Chung</option>
                                <option value="ict" {{ request('orientation') == 'ict' ? 'selected' : '' }}>ICT</option>
                                <option value="cs" {{ request('orientation') == 'cs' ? 'selected' : '' }}>CS</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Dạng câu hỏi</label>
                            <select name="type" class="form-select form-select-sm border-0 shadow-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="single_choice" {{ request('type') == 'single_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                                <option value="true_false_group" {{ request('type') == 'true_false_group' ? 'selected' : '' }}>Đúng/Sai chùm</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">Mức độ</label>
                            <select name="cognitive_level_id" class="form-select form-select-sm border-0 shadow-sm">
                                <option value="">-- Tất cả --</option>
                                @foreach($levels as $lv)
                                    <option value="{{ $lv->id }}" {{ request('cognitive_level_id') == $lv->id ? 'selected' : '' }}>
                                        {{ $lv->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Hàng 2 --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Năng lực</label>
                            <select name="competency_id" class="form-select form-select-sm border-0 shadow-sm">
                                <option value="">-- Tất cả năng lực --</option>
                                @foreach($competencies as $comp)
                                    <option value="{{ $comp->id }}" {{ request('competency_id') == $comp->id ? 'selected' : '' }}>
                                        {{ $comp->code }}: {{ $comp->description }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 text-end pt-2">
                            <a href="{{ route('teacher.exams.create') }}" class="btn btn-light btn-sm me-2 text-muted">
                                <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">
                                <i class="bi bi-search me-1"></i> Tìm kiếm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 2. KHU VỰC CHỌN CÂU HỎI VÀ TẠO ĐỀ --}}
        <form action="{{ route('teacher.exams.store') }}" method="POST" id="createExamForm">
            @csrf
            
            <div class="row g-4">
                {{-- Cột Trái: Danh sách câu hỏi --}}
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-list-task text-primary me-2"></i> Kết quả tìm kiếm ({{ $questions->total() }})
                            </h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                                <label class="form-check-label small fw-bold text-muted" for="checkAll">Chọn tất cả trang này</label>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light text-secondary small text-uppercase">
                                    <tr>
                                        <th width="50" class="text-center">#</th>
                                        <th>Nội dung câu hỏi</th>
                                        <th width="150">Phân loại</th>
                                        <th width="80" class="text-center">Xem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($questions as $q)
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input question-checkbox" 
                                                       type="checkbox" 
                                                       value="{{ $q->id }}" 
                                                       onchange="toggleQuestion('{{ $q->id }}', this.checked)">
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 480px;">
                                                    {{ Str::limit($q->content, 120) }}
                                                </div>
                                                @if($q->competency)
                                                    <div class="small text-muted mt-1">
                                                        <i class="bi bi-lightning-charge-fill text-warning"></i> {{ $q->competency->code }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border me-1">Lớp {{ $q->grade }}</span>
                                                @if($q->type == 'single_choice')
                                                    <span class="badge badge-type-sc text-white">TN</span>
                                                @else
                                                    <span class="badge badge-type-tf text-white">Đ/S</span>
                                                @endif
                                                <div class="small text-muted mt-1 text-truncate" style="max-width: 150px;">
                                                    {{ $q->topic->name ?? 'Chưa phân loại' }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="opacity-25 mb-3">
                                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                                </div>
                                                <h6 class="text-muted">Không tìm thấy câu hỏi nào phù hợp.</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-footer bg-white border-top-0 py-3">
                            {{ $questions->withQueryString()->links() }} 
                        </div>
                    </div>
                </div>

                {{-- Cột Phải: Form thông tin đề thi (Sticky) --}}
                <div class="col-md-4">
                    <div class="card shadow-lg border-0 rounded-4 sticky-sidebar">
                        <div class="card-header card-header-custom text-white fw-bold py-3 rounded-top-4">
                            <i class="bi bi-pencil-square me-2"></i> Thông tin đề thi
                        </div>
                        <div class="card-body p-4">
                            {{-- Tên đề --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Tên đề thi <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="VD: Kiểm tra 15 phút Tin học 11" required>
                            </div>
                            
                            {{-- Thời gian --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Thời gian (phút) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-clock"></i></span>
                                    <input type="number" name="duration" class="form-control" value="45" min="5" required>
                                </div>
                            </div>

                            {{-- [MỚI] TRẠNG THÁI CÔNG KHAI / RIÊNG TƯ --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark mb-2">Trạng thái phát hành</label>
                                <div class="card p-3 border shadow-sm bg-light">
                                    <div class="form-check form-switch d-flex align-items-center">
                                        <input class="form-check-input me-3" type="checkbox" name="is_public" id="isPublicSwitch" value="1" style="transform: scale(1.5);">
                                        <div>
                                            <label class="form-check-label fw-bold text-primary" for="isPublicSwitch" style="cursor: pointer">Công khai (Publish)</label>
                                            <div class="small text-muted" id="statusHelpText">
                                                <i class="bi bi-lock-fill"></i> Riêng tư: Chỉ mình bạn thấy.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Hiển thị số lượng đã chọn --}}
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3" style="background-color: #eef2ff; border: 1px dashed #6366f1;">
                                <div>
                                    <div class="small text-muted text-uppercase fw-bold">Đã chọn</div>
                                    <div class="h3 fw-bold text-primary mb-0" id="countSelected">0</div>
                                </div>
                                <i class="bi bi-check-circle-fill text-primary" style="font-size: 2rem; opacity: 0.5;"></i>
                            </div>

                            {{-- Input ẩn chứa danh sách ID --}}
                            <input type="hidden" name="question_ids" id="finalQuestionIds">

                            <button type="button" onclick="submitExamForm()" class="btn btn-primary w-100 fw-bold py-3 shadow-lg rounded-3">
                                <i class="bi bi-save me-1"></i> HOÀN TẤT TẠO ĐỀ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // --- LOGIC GIỎ HÀNG (CÂU HỎI) GIỮ NGUYÊN ---
        let selectedQuestions = JSON.parse(localStorage.getItem('exam_cart')) || [];

        document.addEventListener("DOMContentLoaded", function() {
            updateUI();
            
            // Tích vào các checkbox đã được lưu
            document.querySelectorAll('.question-checkbox').forEach(cb => {
                if (selectedQuestions.includes(cb.value)) {
                    cb.checked = true;
                }
            });

            // --- [MỚI] SCRIPT CHO NÚT GẠT PUBLIC/PRIVATE ---
            const switchBtn = document.getElementById('isPublicSwitch');
            const helpText = document.getElementById('statusHelpText');
            
            if(switchBtn) {
                switchBtn.addEventListener('change', function() {
                    if(this.checked) {
                        helpText.innerHTML = '<i class="bi bi-globe"></i> Công khai: Học sinh có thể thấy và làm bài.';
                        helpText.classList.remove('text-muted');
                        helpText.classList.add('text-success');
                    } else {
                        helpText.innerHTML = '<i class="bi bi-lock-fill"></i> Riêng tư: Chỉ mình bạn thấy.';
                        helpText.classList.remove('text-success');
                        helpText.classList.add('text-muted');
                    }
                });
            }
        });

        // Xử lý tích chọn từng cái
        function toggleQuestion(id, isChecked) {
            id = String(id);
            if (isChecked) {
                if (!selectedQuestions.includes(id)) selectedQuestions.push(id);
            } else {
                selectedQuestions = selectedQuestions.filter(item => item !== id);
            }
            saveToStorage();
        }

        // Chọn tất cả
        const checkAllBox = document.getElementById('checkAll');
        if(checkAllBox) {
            checkAllBox.addEventListener('change', function() {
                let isChecked = this.checked;
                document.querySelectorAll('.question-checkbox').forEach(cb => {
                    cb.checked = isChecked;
                    toggleQuestion(cb.value, isChecked);
                });
            });
        }

        // Lưu và cập nhật UI
        function saveToStorage() {
            localStorage.setItem('exam_cart', JSON.stringify(selectedQuestions));
            updateUI();
        }

        function updateUI() {
            const countSpan = document.getElementById('countSelected');
            if(countSpan) countSpan.innerText = selectedQuestions.length;
        }

        // Submit Form
        function submitExamForm() {
            if (selectedQuestions.length === 0) {
                alert("Bạn chưa chọn câu hỏi nào!");
                return;
            }
            // Gán dữ liệu vào input ẩn
            document.getElementById('finalQuestionIds').value = selectedQuestions.join(',');
            
            // Xóa storage để lần sau tạo mới không bị dính cũ
            localStorage.removeItem('exam_cart'); 
            
            document.getElementById('createExamForm').submit();
        }
    </script>
    @endpush

</x-layouts.teacher>