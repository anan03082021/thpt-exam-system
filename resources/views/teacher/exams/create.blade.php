<x-layouts.teacher title="Tạo đề thi & Chọn câu hỏi">

    @push('styles')
    <style>
        /* Tông màu Indigo chủ đạo */
        .card-header-custom { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; }
        .bg-indigo-50 { background-color: #eef2ff; }
        .filter-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; }
        .question-checkbox { width: 1.2em; height: 1.2em; cursor: pointer; border-color: #cbd5e1; }
        .question-checkbox:checked { background-color: #4f46e5; border-color: #4f46e5; }
        .sticky-sidebar { top: 100px; z-index: 99; transition: all 0.3s; }
        
        /* Badges */
        .badge-type-sc { background-color: #0ea5e9; }
        .badge-type-tf { background-color: #f59e0b; }
        .badge-orient-chung { background-color: #64748b; }
        .badge-orient-cs { background-color: #4f46e5; }
        .badge-orient-ict { background-color: #059669; }

        /* Tabs */
        .nav-tabs .nav-link { border: none; color: #64748b; font-weight: 700; padding: 1rem 1.5rem; transition: all 0.2s; }
        .nav-tabs .nav-link:hover { color: #4f46e5; background-color: #f8fafc; }
        .nav-tabs .nav-link.active { color: #4f46e5; border-bottom: 3px solid #4f46e5; background: white; }
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

    {{-- 
        ========================================================
        ALPINE JS STORE (QUẢN LÝ TOÀN BỘ TRẠNG THÁI TẠI ĐÂY)
        ========================================================
    --}}
    <div class="container-fluid p-0" 
         x-data="{ 
            activeTab: localStorage.getItem('exam_active_tab') || 'bank', 
            
            // 1. Mảng câu hỏi từ NGÂN HÀNG (Lấy từ LocalStorage)
            bankQuestions: JSON.parse(localStorage.getItem('exam_cart_v2') || '[]'),
            
            // 2. Mảng câu hỏi MỚI SOẠN (Lấy từ LocalStorage)
            newQuestions: JSON.parse(localStorage.getItem('exam_new_draft') || '[]'),

            // --- FUNCTION: Chuyển Tab ---
            setTab(tab) {
                this.activeTab = tab;
                localStorage.setItem('exam_active_tab', tab);
            },

            // --- FUNCTION: Kiểm tra câu hỏi đã chọn chưa (Dùng cho UI checkbox) ---
            inBank(id) {
                return this.bankQuestions.some(q => q.id == id);
            },

            // --- FUNCTION: Toggle chọn/bỏ chọn câu hỏi ngân hàng ---
            toggleBank(id, type) {
                id = String(id); // Ép kiểu string để so sánh chuẩn
                if (this.inBank(id)) {
                    // Nếu có rồi -> Xóa
                    this.bankQuestions = this.bankQuestions.filter(q => q.id != id);
                } else {
                    // Nếu chưa có -> Thêm
                    this.bankQuestions.push({ id: id, type: type.toLowerCase() });
                }
            },

            // --- FUNCTION: Xử lý nút 'Chọn tất cả trang này' ---
            toggleAllPage(isChecked) {
                // Lấy tất cả checkbox câu hỏi đang hiển thị trên trang hiện tại
                const checkboxes = document.querySelectorAll('.question-checkbox-item');
                checkboxes.forEach(cb => {
                    let id = cb.value;
                    let type = cb.dataset.type;
                    if (isChecked && !this.inBank(id)) {
                        this.bankQuestions.push({ id: id, type: type });
                    } else if (!isChecked && this.inBank(id)) {
                        this.bankQuestions = this.bankQuestions.filter(q => q.id != id);
                    }
                });
            },

            // --- COMPUTED: Tính toán số lượng (GỘP CẢ 2 NGUỒN) ---
            get totalCount() { return this.bankQuestions.length + this.newQuestions.length; },
            
            get countChung() { 
                let b = this.bankQuestions.filter(q => q.type == 'chung' || !q.type).length;
                let n = this.newQuestions.filter(q => q.orientation == 'chung').length;
                return b + n;
            },
            get countCS() { 
                let b = this.bankQuestions.filter(q => q.type == 'cs').length;
                let n = this.newQuestions.filter(q => q.orientation == 'cs').length;
                return b + n;
            },
            get countICT() { 
                let b = this.bankQuestions.filter(q => q.type == 'ict').length;
                let n = this.newQuestions.filter(q => q.orientation == 'ict').length;
                return b + n;
            },

            // --- ACTION: Submit Form ---
            submitForm() {
                if (this.totalCount === 0) {
                    alert('Bạn chưa chọn hoặc tạo câu hỏi nào!');
                    return;
                }
                
                // Đổ ID ngân hàng vào input hidden
                let ids = this.bankQuestions.map(q => q.id);
                document.getElementById('finalQuestionIds').value = ids.join(',');

                // Xóa localStorage
                localStorage.removeItem('exam_cart_v2'); 
                localStorage.removeItem('exam_new_draft'); 
                localStorage.removeItem('exam_active_tab');

                document.getElementById('createExamForm').submit();
            }
         }"
         
         {{-- Watchers để tự động lưu khi biến thay đổi --}}
         x-init="
            $watch('bankQuestions', val => localStorage.setItem('exam_cart_v2', JSON.stringify(val)));
            $watch('newQuestions', val => localStorage.setItem('exam_new_draft', JSON.stringify(val)));
         ">
        
        {{-- 1. BỘ LỌC CÂU HỎI (Chỉ hiện ở Tab Ngân hàng) --}}
        <div class="card shadow-sm mb-4 border-0 rounded-4 overflow-hidden" x-show="activeTab === 'bank'" x-transition>
            <div class="card-header bg-white fw-bold border-bottom py-3">
                <i class="bi bi-funnel-fill text-primary me-2"></i> Bộ lọc câu hỏi
            </div>
            <div class="card-body filter-box m-3">
                <form action="{{ route('teacher.exams.create') }}" method="GET">
                    <div class="row g-3">
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
                                    <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
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
                                    <option value="{{ $lv->id }}" {{ request('cognitive_level_id') == $lv->id ? 'selected' : '' }}>{{ $lv->name }}</option>
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

        {{-- 2. KHU VỰC CHÍNH (FORM) --}}
        <form action="{{ route('teacher.exams.store') }}" method="POST" id="createExamForm">
            @csrf
            
            <div class="row g-4">
                {{-- CỘT TRÁI: DANH SÁCH & SOẠN THẢO --}}
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5" style="min-height: 600px;">
                        
                        {{-- NAV TABS --}}
                        <div class="card-header bg-white border-bottom p-0 sticky-top" style="z-index: 10;">
                            <ul class="nav nav-tabs nav-fill card-header-tabs m-0">
                                <li class="nav-item">
                                    <a class="nav-link cursor-pointer" :class="{ 'active': activeTab === 'bank' }" @click.prevent="setTab('bank')">
                                       <i class="bi bi-database me-2"></i> Ngân hàng câu hỏi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link cursor-pointer" :class="{ 'active': activeTab === 'new' }" @click.prevent="setTab('new')">
                                       <i class="bi bi-plus-circle me-2"></i> Soạn câu hỏi mới
                                       <span class="badge bg-danger rounded-pill ms-1" x-show="newQuestions.length > 0" x-text="newQuestions.length"></span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body bg-white p-0">
                            
                            {{-- === TAB 1: NGÂN HÀNG CÂU HỎI === --}}
                            <div x-show="activeTab === 'bank'" class="p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                                    <h6 class="mb-0 fw-bold text-dark">
                                        <i class="bi bi-list-task text-primary me-2"></i> Kết quả tìm kiếm ({{ $questions->total() }})
                                    </h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="checkAll" @change="toggleAllPage($event.target.checked)">
                                        <label class="form-check-label small fw-bold text-muted" for="checkAll">Chọn tất cả trang này</label>
                                    </div>
                                </div>

                                <div class="table-responsive border rounded-3">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="bg-light text-secondary small text-uppercase">
                                            <tr>
                                                <th width="50" class="text-center">#</th>
                                                <th>Nội dung câu hỏi</th>
                                                <th width="160">Phân loại</th>
                                                <th width="60" class="text-center">Xem</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($questions as $q)
                                                <tr>
                                                    <td class="text-center bg-light">
                                                        {{-- CHECKBOX: Đã được fix để dùng Alpine --}}
                                                        <input class="form-check-input question-checkbox question-checkbox-item" 
                                                               type="checkbox" 
                                                               value="{{ $q->id }}" 
                                                               data-type="{{ $q->orientation ?? 'chung' }}"
                                                               :checked="inBank({{ $q->id }})"
                                                               @change="toggleBank('{{ $q->id }}', '{{ $q->orientation ?? 'chung' }}')">
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold text-dark text-truncate" style="max-width: 450px;">{{ Str::limit($q->content, 120) }}</div>
                                                        @if($q->competency)
                                                            <div class="small text-muted mt-1"><i class="bi bi-lightning-charge-fill text-warning"></i> {{ $q->competency->code }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="mb-1">
                                                            @if($q->orientation == 'cs') <span class="badge badge-orient-cs text-white border">CS</span>
                                                            @elseif($q->orientation == 'ict') <span class="badge badge-orient-ict text-white border">ICT</span>
                                                            @else <span class="badge badge-orient-chung text-white border">Chung</span> @endif
                                                            <span class="badge bg-white text-dark border">Lớp {{ $q->grade }}</span>
                                                        </div>
                                                        <div>
                                                            @if($q->type == 'single_choice') <span class="badge badge-type-sc text-white">TN</span>
                                                            @else <span class="badge badge-type-tf text-white">Đ/S</span> @endif
                                                            <span class="small text-muted ms-1">{{ Str::limit($q->topic->name ?? '', 15) }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary border-0 rounded-circle" 
                                                                data-question='@json($q)' onclick="showQuestionPreview(this)">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4" class="text-center py-5 text-muted">Không tìm thấy câu hỏi nào.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3 px-2">{{ $questions->withQueryString()->links() }}</div>
                            </div>

                            {{-- === TAB 2: SOẠN CÂU HỎI MỚI (Giao diện chuẩn theo ảnh) === --}}
                            <div x-show="activeTab === 'new'" class="p-4 bg-light" style="min-height: 500px; display: none;">
                                
                                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
                                    <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
                                    <div>
                                        <strong>Chế độ soạn thảo chi tiết:</strong>
                                        <div class="small">Thông tin sẽ được tự động lưu nháp để tránh mất dữ liệu khi tải lại trang.</div>
                                    </div>
                                </div>

                                {{-- Loop Form Câu hỏi mới --}}
                                <template x-for="(q, index) in newQuestions" :key="index">
                                    <div class="card mb-4 shadow-sm border-0 rounded-3">
                                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                                            <h6 class="mb-0 fw-bold" :class="q.type === 'single_choice' ? 'text-primary' : 'text-warning'">
                                                <i class="bi bi-pencil-square me-2"></i> 
                                                <span x-text="q.type === 'single_choice' ? 'Trắc nghiệm' : 'Đúng/Sai chùm'"></span> #<span x-text="index + 1"></span>
                                            </h6>
                                            <button type="button" class="btn btn-outline-danger btn-sm border-0 bg-light text-danger" 
                                                    @click="newQuestions.splice(index, 1);" title="Xóa câu này">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <div class="card-body p-4">
                                            {{-- 1. THÔNG TIN PHÂN LOẠI --}}
                                            <div class="mb-4">
                                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-tags me-2 text-secondary"></i> 1. Thông tin phân loại</h6>
                                                <div class="bg-indigo-50 p-3 rounded-3 border border-indigo-100">
                                                    
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold small text-muted">Lớp <span class="text-danger">*</span></label>
                                                            <select :name="`new_questions[${index}][grade]`" class="form-select bg-white" x-model="q.grade" required>
                                                                <option value="10">Lớp 10</option>
                                                                <option value="11">Lớp 11</option>
                                                                <option value="12">Lớp 12</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold small text-muted">Định hướng <span class="text-danger">*</span></label>
                                                            {{-- Select định hướng --}}
                                                            <select :name="`new_questions[${index}][orientation]`" class="form-select bg-white" x-model="q.orientation" required>
                                                                <option value="chung">Chung</option>
                                                                <option value="cs">Khoa học máy tính (CS)</option>
                                                                <option value="ict">Tin học ứng dụng (ICT)</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold small text-muted">Chủ đề <span class="text-danger">*</span></label>
                                                            <select :name="`new_questions[${index}][topic_id]`" class="form-select bg-white" x-model="q.topic_id" required>
                                                                <option value="">-- Chọn chủ đề --</option>
                                                                @foreach($topics as $topic)
                                                                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold small text-muted">Nội dung cốt lõi</label>
                                                            <select :name="`new_questions[${index}][core_content]`" class="form-select bg-white" x-model="q.core_content">
                                                                <option value="">-- Vui lòng chọn chủ đề trước --</option>
                                                                <option value="1">Máy tính và xã hội tri thức</option>
                                                                <option value="2">Mạng máy tính và Internet</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold small text-muted">Yêu cầu cần đạt</label>
                                                            <select :name="`new_questions[${index}][yccd]`" class="form-select bg-white" x-model="q.yccd">
                                                                <option value="">-- Vui lòng chọn chủ đề trước --</option>
                                                                <option value="1">Biết được vai trò của máy tính</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold small text-muted">Năng lực <span class="text-danger">*</span></label>
                                                            <select :name="`new_questions[${index}][competency_id]`" class="form-select bg-white" x-model="q.competency_id" required>
                                                                <option value="">-- Chọn năng lực --</option>
                                                                @foreach($competencies as $comp)
                                                                    <option value="{{ $comp->id }}">{{ $comp->code }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold small text-muted">Loại câu hỏi <span class="text-danger">*</span></label>
                                                            <select :name="`new_questions[${index}][type]`" class="form-select bg-white fw-bold text-primary" x-model="q.type" required>
                                                                <option value="single_choice">Dạng 1: Trắc nghiệm</option>
                                                                <option value="true_false_group">Dạng 2: Đúng/Sai chùm</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold small text-muted">Mức độ <span class="text-danger">*</span></label>
                                                            <select :name="`new_questions[${index}][level]`" class="form-select bg-white" x-model="q.level" required>
                                                                <option value="easy">Nhận biết</option>
                                                                <option value="medium">Thông hiểu</option>
                                                                <option value="hard">Vận dụng</option>
                                                                <option value="very_hard">Vận dụng cao</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="text-muted opacity-25">

                                            {{-- 2. NỘI DUNG CÂU HỎI --}}
                                            <div>
                                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-file-text me-2 text-secondary"></i> 2. Nội dung & Đáp án</h6>
                                                
                                                <div class="mb-4">
                                                    <label class="form-label small fw-bold text-muted">Câu dẫn / Nội dung chính <span class="text-danger">*</span></label>
                                                    <textarea :name="`new_questions[${index}][content]`" class="form-control" rows="3" 
                                                              x-model="q.content" placeholder="Nhập nội dung câu hỏi..." required></textarea>
                                                </div>

                                                {{-- A. Dạng Trắc nghiệm --}}
                                                <div x-show="q.type === 'single_choice'" x-transition>
                                                    <label class="form-label small fw-bold text-muted mb-2">Các phương án (Chọn 1 đáp án đúng)</label>
                                                    <div class="row g-2">
                                                        <template x-for="(opt, i) in q.options" :key="i">
                                                            <div class="col-md-6">
                                                                <div class="input-group">
                                                                    <div class="input-group-text bg-white border-end-0">
                                                                        <input class="form-check-input mt-0" type="radio" 
                                                                               :name="`new_questions[${index}][correct_index]`" 
                                                                               :value="i" x-model="q.correct_index" required>
                                                                    </div>
                                                                    <input type="text" class="form-control border-start-0" 
                                                                           :name="`new_questions[${index}][options][]`" 
                                                                           x-model="q.options[i]" :placeholder="`Phương án ${String.fromCharCode(65+i)}`" required>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                {{-- B. Dạng Đúng/Sai --}}
                                                <div x-show="q.type === 'true_false_group'" x-transition>
                                                    <label class="form-label small fw-bold text-muted mb-2">Các ý nhận định (Chọn Đúng hoặc Sai cho từng ý)</label>
                                                    <div class="border rounded-3 overflow-hidden">
                                                        <template x-for="(item, i) in q.tf_items" :key="i">
                                                            <div class="d-flex align-items-center p-2 border-bottom bg-white">
                                                                <span class="fw-bold me-2 px-3 text-secondary bg-light rounded py-1" x-text="String.fromCharCode(97+i)"></span>
                                                                
                                                                <input type="text" class="form-control border-0 me-3 shadow-none" 
                                                                       :name="`new_questions[${index}][tf_items][${i}][content]`"
                                                                       x-model="item.content" placeholder="Nhập ý nhận định..." required>
                                                                
                                                                <div class="btn-group shadow-sm" role="group">
                                                                    <input type="radio" class="btn-check" :name="`new_questions[${index}][tf_items][${i}][is_correct]`" 
                                                                           :id="`q${index}_opt${i}_false`" value="0" x-model="item.is_correct" autocomplete="off">
                                                                    <label class="btn btn-outline-danger btn-sm px-3" :for="`q${index}_opt${i}_false`">Sai</label>
                                                                
                                                                    <input type="radio" class="btn-check" :name="`new_questions[${index}][tf_items][${i}][is_correct]`" 
                                                                           :id="`q${index}_opt${i}_true`" value="1" x-model="item.is_correct" autocomplete="off">
                                                                    <label class="btn btn-outline-success btn-sm px-3" :for="`q${index}_opt${i}_true`">Đúng</label>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- Nút Thêm --}}
                                <button type="button" class="btn btn-white border-dashed w-100 py-4 text-primary fw-bold shadow-sm hover-shadow transition-all" 
                                        style="border: 2px dashed #a5b4fc; background-color: #f8fafc;"
                                        @click="newQuestions.push({ 
                                            grade: '10', orientation: 'chung', topic_id: '', competency_id: '', core_content: '', yccd: '', level: 'medium',
                                            type: 'single_choice', 
                                            content: '', 
                                            options: ['', '', '', ''], correct_index: 0,
                                            tf_items: [{content: '', is_correct: 0}, {content: '', is_correct: 0}, {content: '', is_correct: 0}, {content: '', is_correct: 0}]
                                        });">
                                    <i class="bi bi-plus-circle-fill me-2 fs-5 align-middle"></i> THÊM CÂU HỎI MỚI
                                </button>
                                
                                <div class="text-center mt-3 text-muted small" x-show="newQuestions.length === 0">
                                    Chưa có câu hỏi mới nào. Nhấn nút trên để bắt đầu soạn thảo.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI: SIDEBAR THÔNG TIN (GỘP SỐ LƯỢNG) --}}
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
                                <input type="number" name="duration" class="form-control" value="45" min="5" required>
                            </div>
                            
                            {{-- Trạng thái --}}
                            <div class="card p-3 border shadow-sm bg-light mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_public" id="isPublicSwitch" value="1">
                                    <label class="form-check-label fw-bold text-primary" for="isPublicSwitch">Công khai (Publish)</label>
                                </div>
                            </div>

                            {{-- TỔNG HỢP SỐ LƯỢNG (Đã gộp chung Bank + New) --}}
                            <div class="p-3 rounded-3 mb-3 bg-white border border-primary border-opacity-25" style="border-style: dashed !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                                    <span class="fw-bold text-dark small text-uppercase">Tổng số câu</span>
                                    <span class="h4 fw-bold text-primary mb-0" x-text="totalCount">0</span>
                                </div>
                                <div class="small">
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span class="text-muted"><i class="bi bi-layers me-1"></i> Kiến thức Chung:</span>
                                        <span class="badge badge-orient-chung border text-white px-2 py-1 rounded-pill" x-text="countChung">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span class="text-primary fw-bold"><i class="bi bi-cpu me-1"></i> Khoa học máy tính (CS):</span>
                                        <span class="badge badge-orient-cs border text-white px-2 py-1 rounded-pill" x-text="countCS">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-success fw-bold"><i class="bi bi-laptop me-1"></i> Tin học ứng dụng (ICT):</span>
                                        <span class="badge badge-orient-ict border text-white px-2 py-1 rounded-pill" x-text="countICT">0</span>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="question_ids" id="finalQuestionIds">

                            <button type="button" @click="submitForm" class="btn btn-primary w-100 fw-bold py-3 shadow-lg rounded-3">
                                <i class="bi bi-save me-1"></i> HOÀN TẤT TẠO ĐỀ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    {{-- MODAL PREVIEW (Dành cho câu hỏi trong ngân hàng) --}}
    <div class="modal fade" id="questionPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-search me-2"></i> Chi tiết câu hỏi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="previewContent"></div>
                <div class="modal-footer bg-light"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button></div>
            </div>
        </div>
    </div>

    <script>
        // Modal Preview Logic
        function showQuestionPreview(button) {
            const q = JSON.parse(button.getAttribute('data-question'));
            const modalBody = document.getElementById('previewContent');
            let html = '';
            let typeBadge = q.type === 'single_choice' ? '<span class="badge bg-info text-dark">Trắc nghiệm</span>' : '<span class="badge bg-warning text-dark">Đúng/Sai chùm</span>';
            html += `<div class="d-flex justify-content-between align-items-center mb-3">${typeBadge}<span class="badge bg-light text-secondary border">ID: ${q.id}</span></div>`;
            html += `<div class="p-3 bg-light rounded-3 border mb-4"><h6 class="fw-bold text-primary mb-2">Nội dung:</h6><div class="fs-5">${q.content}</div></div>`;
            html += '<h6 class="fw-bold text-dark mb-3">Đáp án:</h6>';
            if (q.type === 'single_choice' && q.answers) {
                html += '<div class="list-group">';
                q.answers.forEach((ans) => {
                    let itemClass = ans.is_correct == 1 ? 'list-group-item-success fw-bold border-success' : 'list-group-item-light';
                    let icon = ans.is_correct == 1 ? '<i class="bi bi-check-circle-fill text-success me-2"></i>' : '<i class="bi bi-circle text-muted me-2"></i>';
                    html += `<div class="list-group-item ${itemClass}">${icon} ${ans.content}</div>`;
                });
                html += '</div>';
            } else if (q.type === 'true_false_group' && q.children) {
                html += '<div class="table-responsive"><table class="table table-bordered align-middle"><thead class="table-light"><tr><th>Ý nhận định</th><th class="text-center" width="100">Đáp án</th></tr></thead><tbody>';
                q.children.forEach(child => {
                    let correctAns = child.answers.find(a => a.is_correct == 1);
                    let resultText = correctAns ? correctAns.content : 'N/A';
                    let badgeClass = resultText === 'Đúng' ? 'bg-success' : 'bg-danger';
                    html += `<tr><td>${child.content}</td><td class="text-center"><span class="badge ${badgeClass}">${resultText}</span></td></tr>`;
                });
                html += '</tbody></table></div>';
            }
            modalBody.innerHTML = html;
            new bootstrap.Modal(document.getElementById('questionPreviewModal')).show();
        }
    </script>
    @endpush

</x-layouts.teacher>