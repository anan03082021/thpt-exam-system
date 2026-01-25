<x-layouts.teacher title="Tạo đề thi & Chọn câu hỏi">

    @push('styles')
    <style>
        /* GIỮ NGUYÊN CSS CHUẨN CŨ */
        :root { --primary-color: #4f46e5; --primary-bg: #eef2ff; }
        
        /* Card Styles */
        .card { border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.02), 0 4px 10px rgba(0,0,0,0.03); border-radius: 12px; }
        .card-header-custom { background: white; border-bottom: 1px solid #f1f5f9; padding: 1rem 1.5rem; }
        
        /* Form Inputs */
        .form-control, .form-select { border-color: #e2e8f0; padding: 0.75rem 1rem; font-size: 0.95rem; border-radius: 8px; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .form-label { font-weight: 600; color: #334155; font-size: 0.85rem; margin-bottom: 0.5rem; }

        /* Badges & Elements */
        .badge { padding: 0.5em 0.8em; font-weight: 600; letter-spacing: 0.3px; }
        .badge-source-thpt { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-source-user { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        
        .badge-type-sc { background-color: #0ea5e9; } 
        .badge-type-tf { background-color: #f59e0b; }
        .badge-orient-chung { background-color: #64748b; }
        .badge-orient-cs { background-color: #4f46e5; }
        .badge-orient-ict { background-color: #059669; }

        /* Stats Box Top */
        .stats-card { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; transition: all 0.3s; }
        .stats-card:hover { border-color: var(--primary-color); background: #fff; }

        /* Checkbox */
        .question-checkbox { width: 1.3em; height: 1.3em; cursor: pointer; border: 2px solid #cbd5e1; border-radius: 4px; }
        .question-checkbox:checked { background-color: var(--primary-color); border-color: var(--primary-color); }

        /* Utilities */
        .cursor-pointer { cursor: pointer; }
        .sticky-header { position: sticky; top: 0; z-index: 100; background: rgba(255,255,255,0.95); backdrop-filter: blur(5px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    </style>
    @endpush

    {{-- ALPINE JS STORE --}}
    <div class="container-fluid p-0" 
         x-data="{ 
            activeTab: localStorage.getItem('exam_active_tab') || 'bank', 
            bankQuestions: JSON.parse(localStorage.getItem('exam_cart_v2') || '[]'),
            newQuestions: JSON.parse(localStorage.getItem('exam_new_draft') || '[]'),

            setTab(tab) {
                this.activeTab = tab;
                localStorage.setItem('exam_active_tab', tab);
            },

            inBank(id) { return this.bankQuestions.some(q => q.id == id); },

            toggleBank(id, type, source) {
                id = String(id);
                if (this.inBank(id)) {
                    this.bankQuestions = this.bankQuestions.filter(q => q.id != id);
                } else {
                    this.bankQuestions.push({ id: id, type: type.toLowerCase(), source: source });
                }
            },

            toggleAllPage(isChecked) {
                const checkboxes = document.querySelectorAll('.question-checkbox-item');
                checkboxes.forEach(cb => {
                    let id = cb.value;
                    let type = cb.dataset.type;
                    let source = cb.dataset.source;
                    if (isChecked && !this.inBank(id)) {
                        this.bankQuestions.push({ id: id, type: type, source: source });
                    } else if (!isChecked && this.inBank(id)) {
                        this.bankQuestions = this.bankQuestions.filter(q => q.id != id);
                    }
                });
            },

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
            get countTHPT() {
                return this.bankQuestions.filter(q => q.source == 'thpt_2025').length;
            },

            submitForm() {
                if (this.totalCount === 0) {
                    alert('Bạn chưa chọn câu hỏi nào!');
                    return;
                }
                let ids = this.bankQuestions.map(q => q.id);
                document.getElementById('finalQuestionIds').value = ids.join(',');
                localStorage.removeItem('exam_cart_v2'); 
                localStorage.removeItem('exam_new_draft'); 
                localStorage.removeItem('exam_active_tab');
                document.getElementById('createExamForm').submit();
            }
         }"
         x-init="$watch('bankQuestions', val => localStorage.setItem('exam_cart_v2', JSON.stringify(val))); $watch('newQuestions', val => localStorage.setItem('exam_new_draft', JSON.stringify(val)));">
        
        {{-- [QUAN TRỌNG] ĐÃ XÓA THẺ FORM BAO QUANH Ở ĐÂY ĐỂ TRÁNH LỖI --}}
            
            {{-- === PHẦN 1: HEADER THÔNG TIN ĐỀ THI === --}}
            <div class="card mb-4 border-0 shadow-sm sticky-header">
                <div class="card-body p-4">
                    <div class="row g-4 align-items-end">
                        {{-- Cột 1: Tên đề thi --}}
                        <div class="col-md-5">
                            <label class="form-label text-uppercase text-secondary small ls-1">Tên đề thi <span class="text-danger">*</span></label>
                            {{-- THÊM thuộc tính form="createExamForm" --}}
                            <input type="text" name="title" form="createExamForm" class="form-control fw-bold text-primary" placeholder="VD: Kiểm tra Giữa kỳ 1 Tin học 12..." style="font-size: 1.1rem;" required>
                        </div>
                        
                        {{-- Cột 2: Thời gian & Công khai --}}
                        <div class="col-md-4">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label text-uppercase text-secondary small ls-1">Thời gian</label>
                                    <div class="input-group">
                                        {{-- THÊM thuộc tính form="createExamForm" --}}
                                        <input type="number" name="duration" form="createExamForm" class="form-control fw-bold" value="45" min="5" required>
                                        <span class="input-group-text bg-light text-muted small">phút</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-uppercase text-secondary small ls-1">Trạng thái</label>
                                    {{-- THÊM thuộc tính form="createExamForm" --}}
                                    <select name="is_public" form="createExamForm" class="form-select fw-bold text-success bg-success bg-opacity-10 border-success border-opacity-25">
                                        <option value="1">Công khai</option>
                                        <option value="0">Nháp (Ẩn)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Cột 3: Nút Lưu & Thống kê tổng --}}
                        <div class="col-md-3 text-end">
                            <div class="d-flex flex-column align-items-end gap-2">
                                <span class="fw-bold text-dark" style="font-size: 0.9rem;">
                                    Đã chọn: <span class="badge bg-primary rounded-pill fs-6" x-text="totalCount">0</span> câu
                                </span>
                                {{-- Nút này gọi JS, không cần form attribute --}}
                                <button type="button" @click="submitForm" class="btn btn-primary fw-bold w-100 py-2 shadow-sm text-uppercase ls-1">
                                    <i class="bi bi-save2 me-2"></i> Hoàn tất đề thi
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- THANH THỐNG KÊ NHANH --}}
                    <div class="row mt-4 pt-3 border-top g-3">
                        <div class="col-md-3 col-6">
                            <div class="stats-card p-2 d-flex align-items-center justify-content-between px-3">
                                <span class="text-danger fw-bold small"><i class="bi bi-star-fill me-1"></i> Đề THPT 2025</span>
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill" x-text="countTHPT">0</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stats-card p-2 d-flex align-items-center justify-content-between px-3">
                                <span class="text-primary fw-bold small"><i class="bi bi-cpu me-1"></i> Khoa học máy tính (CS)</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill" x-text="countCS">0</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stats-card p-2 d-flex align-items-center justify-content-between px-3">
                                <span class="text-success fw-bold small"><i class="bi bi-laptop me-1"></i> Tin học ứng dụng (ICT)</span>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill" x-text="countICT">0</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stats-card p-2 d-flex align-items-center justify-content-between px-3">
                                <span class="text-secondary fw-bold small"><i class="bi bi-layers me-1"></i> Kiến thức Chung</span>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill" x-text="countChung">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === PHẦN 2: KHU VỰC CHỌN CÂU HỎI === --}}
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5" style="min-height: 600px;">
                
                {{-- TABS --}}
                <div class="card-header bg-white border-bottom px-4 pt-3 pb-0">
                    <ul class="nav nav-tabs card-header-tabs m-0 gap-3">
                        <li class="nav-item">
                            <a class="nav-link cursor-pointer py-3" :class="{ 'active': activeTab === 'bank' }" @click.prevent="setTab('bank')">
                               <i class="bi bi-database me-2"></i> Ngân hàng câu hỏi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cursor-pointer py-3" :class="{ 'active': activeTab === 'new' }" @click.prevent="setTab('new')">
                               <i class="bi bi-pencil-square me-2"></i> Soạn câu hỏi mới
                               <span class="badge bg-danger rounded-pill ms-1" x-show="newQuestions.length > 0" x-text="newQuestions.length"></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body bg-white p-0">
                    
                    {{-- === TAB 1: NGÂN HÀNG CÂU HỎI === --}}
                    <div x-show="activeTab === 'bank'" class="p-0">
                        
                        {{-- BỘ LỌC (FORM ĐỘC LẬP - KHÔNG CÒN BỊ LỒNG) --}}
                        <div class="p-4 bg-light border-bottom">
                            <form action="{{ route('teacher.exams.create') }}" method="GET">
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        <select name="grade" class="form-select form-select-sm bg-white">
                                            <option value="">-- Lớp --</option>
                                            <option value="10" {{ request('grade') == '10' ? 'selected' : '' }}>Lớp 10</option>
                                            <option value="11" {{ request('grade') == '11' ? 'selected' : '' }}>Lớp 11</option>
                                            <option value="12" {{ request('grade') == '12' ? 'selected' : '' }}>Lớp 12</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="source" class="form-select form-select-sm bg-white fw-bold text-danger">
                                            <option value="">-- Nguồn --</option>
                                            <option value="thpt_2025" {{ request('source') == 'thpt_2025' ? 'selected' : '' }}>⭐ Đề THPT 2025</option>
                                            <option value="user" {{ request('source') == 'user' ? 'selected' : '' }}>Giáo viên</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="topic_id" class="form-select form-select-sm bg-white">
                                            <option value="">-- Chủ đề --</option>
                                            @foreach($topics as $topic) <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option> @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="orientation" class="form-select form-select-sm bg-white">
                                            <option value="">-- Định hướng --</option>
                                            <option value="chung" {{ request('orientation') == 'chung' ? 'selected' : '' }}>Chung</option>
                                            <option value="cs" {{ request('orientation') == 'cs' ? 'selected' : '' }}>CS</option>
                                            <option value="ict" {{ request('orientation') == 'ict' ? 'selected' : '' }}>ICT</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="type" class="form-select form-select-sm bg-white">
                                            <option value="">-- Dạng câu --</option>
                                            <option value="single_choice" {{ request('type') == 'single_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                                            <option value="true_false_group" {{ request('type') == 'true_false_group' ? 'selected' : '' }}>Đúng/Sai chùm</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- DANH SÁCH --}}
                        <div class="d-flex justify-content-between align-items-center p-3 px-4 border-bottom bg-white">
                            <span class="text-muted small fw-bold">Tìm thấy {{ $questions->total() }} kết quả</span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="checkAll" @change="toggleAllPage($event.target.checked)">
                                <label class="form-check-label small fw-bold text-dark cursor-pointer" for="checkAll">Chọn tất cả trang này</label>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light text-secondary small text-uppercase">
                                    <tr>
                                        <th width="60" class="text-center">Chọn</th>
                                        <th>Nội dung câu hỏi</th>
                                        <th width="200">Thông tin</th>
                                        <th width="80" class="text-center">Xem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($questions as $q)
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input question-checkbox question-checkbox-item" 
                                                       type="checkbox" value="{{ $q->id }}" 
                                                       data-type="{{ $q->orientation ?? 'chung' }}"
                                                       data-source="{{ $q->source ?? 'user' }}"
                                                       :checked="inBank({{ $q->id }})"
                                                       @change="toggleBank('{{ $q->id }}', '{{ $q->orientation ?? 'chung' }}', '{{ $q->source ?? 'user' }}')">
                                            </td>
                                            <td class="py-3">
                                                <div class="mb-2">
                                                    @if($q->source == 'thpt_2025')
                                                        <span class="badge badge-source-thpt rounded-pill me-1"><i class="bi bi-star-fill me-1"></i> THPT 2025</span>
                                                    @endif
                                                    @if($q->competency)
                                                        <span class="text-muted small"><i class="bi bi-lightning-charge-fill text-warning"></i> {{ $q->competency->code }}</span>
                                                    @endif
                                                </div>
                                                <div class="fw-bold text-dark text-truncate-2" style="font-size: 1rem; line-height: 1.5;">
                                                    {{ Str::limit(strip_tags($q->content), 200) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        <span class="badge bg-white text-dark border">Lớp {{ $q->grade }}</span>
                                                        @if($q->orientation == 'cs') <span class="badge badge-orient-cs text-white">CS</span>
                                                        @elseif($q->orientation == 'ict') <span class="badge badge-orient-ict text-white">ICT</span>
                                                        @else <span class="badge badge-orient-chung text-white">Chung</span> @endif
                                                    </div>
                                                    <div>
                                                        @if($q->type == 'single_choice') <span class="badge badge-type-sc text-white">Trắc nghiệm</span>
                                                        @else <span class="badge badge-type-tf text-white">Đúng/Sai</span> @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-light text-primary rounded-circle border shadow-sm" 
                                                        data-question='@json($q)' onclick="showQuestionPreview(this)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-5 text-muted">Không tìm thấy câu hỏi nào.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">{{ $questions->withQueryString()->links() }}</div>
                    </div>

                    {{-- === TAB 2: SOẠN CÂU HỎI MỚI (DÙNG ATTRIBUTE FORM) === --}}
                    <div x-show="activeTab === 'new'" class="p-4 bg-light" style="min-height: 500px; display: none;">
                        <template x-for="(q, index) in newQuestions" :key="index">
                            <div class="card mb-4 border shadow-sm">
                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                                    <h6 class="mb-0 fw-bold text-primary">
                                        <span class="badge bg-primary me-2">Mới</span> Câu hỏi #<span x-text="index + 1"></span>
                                    </h6>
                                    <button type="button" class="btn btn-outline-danger btn-sm border-0 rounded-circle" @click="newQuestions.splice(index, 1);"><i class="bi bi-x-lg"></i></button>
                                </div>
                                <div class="card-body p-4">
                                    {{-- FORM INPUT (THÊM form="createExamForm") --}}
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-2"><label class="form-label small">Lớp *</label><select :name="`new_questions[${index}][grade]`" form="createExamForm" class="form-select bg-white" x-model="q.grade" required><option value="10">10</option><option value="11">11</option><option value="12">12</option></select></div>
                                        <div class="col-md-2"><label class="form-label small">Định hướng *</label><select :name="`new_questions[${index}][orientation]`" form="createExamForm" class="form-select bg-white" x-model="q.orientation" required><option value="chung">Chung</option><option value="cs">CS</option><option value="ict">ICT</option></select></div>
                                        <div class="col-md-4"><label class="form-label small">Chủ đề *</label><select :name="`new_questions[${index}][topic_id]`" form="createExamForm" class="form-select bg-white" x-model="q.topic_id" required><option value="">-- Chọn --</option>@foreach($topics as $topic) <option value="{{ $topic->id }}">{{ $topic->name }}</option> @endforeach</select></div>
                                        <div class="col-md-4"><label class="form-label small">Loại câu *</label><select :name="`new_questions[${index}][type]`" form="createExamForm" class="form-select bg-white fw-bold" x-model="q.type" required><option value="single_choice">Trắc nghiệm</option><option value="true_false_group">Đúng/Sai chùm</option></select></div>
                                    </div>
                                    {{-- Các trường phụ --}}
                                    <div class="row g-3 mb-3 bg-light p-3 rounded">
                                        <div class="col-md-4"><label class="small text-muted">Nội dung cốt lõi</label><select :name="`new_questions[${index}][core_content]`" form="createExamForm" class="form-select form-select-sm" x-model="q.core_content"><option value="1">Máy tính & XH</option><option value="2">Mạng & Internet</option></select></div>
                                        <div class="col-md-4"><label class="small text-muted">Yêu cầu cần đạt</label><select :name="`new_questions[${index}][yccd]`" form="createExamForm" class="form-select form-select-sm" x-model="q.yccd"><option value="1">Biết vai trò PC</option></select></div>
                                        <div class="col-md-2"><label class="small text-muted">Năng lực</label><select :name="`new_questions[${index}][competency_id]`" form="createExamForm" class="form-select form-select-sm" x-model="q.competency_id">@foreach($competencies as $comp) <option value="{{ $comp->id }}">{{ $comp->code }}</option> @endforeach</select></div>
                                        <div class="col-md-2"><label class="small text-muted">Mức độ</label><select :name="`new_questions[${index}][level]`" form="createExamForm" class="form-select form-select-sm" x-model="q.level"><option value="easy">NB</option><option value="medium">TH</option><option value="hard">VD</option></select></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nội dung câu hỏi</label>
                                        <textarea :name="`new_questions[${index}][content]`" form="createExamForm" class="form-control" rows="3" x-model="q.content" required placeholder="Nhập câu dẫn..."></textarea>
                                    </div>

                                    {{-- Đáp án Trắc nghiệm --}}
                                    <div x-show="q.type === 'single_choice'">
                                        <div class="row g-2">
                                            <template x-for="(opt, i) in q.options" :key="i">
                                                <div class="col-md-6"><div class="input-group"><div class="input-group-text bg-white"><input class="form-check-input mt-0" type="radio" :name="`new_questions[${index}][correct_index]`" form="createExamForm" :value="i" x-model="q.correct_index"></div><input type="text" class="form-control" :name="`new_questions[${index}][options][]`" form="createExamForm" x-model="q.options[i]" :placeholder="`Phương án ${String.fromCharCode(65+i)}`"></div></div>
                                            </template>
                                        </div>
                                    </div>
                                    {{-- Đáp án Đúng Sai --}}
                                    <div x-show="q.type === 'true_false_group'">
                                        <div class="border rounded overflow-hidden">
                                            <template x-for="(item, i) in q.tf_items" :key="i">
                                                <div class="d-flex align-items-center p-2 border-bottom bg-white"><span class="fw-bold me-2 px-3 text-secondary bg-light rounded py-1" x-text="String.fromCharCode(97+i)"></span><input type="text" class="form-control border-0 me-3" :name="`new_questions[${index}][tf_items][${i}][content]`" form="createExamForm" x-model="item.content" placeholder="Nhập ý nhận định..."><div class="btn-group"><input type="radio" class="btn-check" :name="`new_questions[${index}][tf_items][${i}][is_correct]`" form="createExamForm" :id="`q${index}_opt${i}_false`" value="0" x-model="item.is_correct"><label class="btn btn-outline-danger btn-sm" :for="`q${index}_opt${i}_false`">Sai</label><input type="radio" class="btn-check" :name="`new_questions[${index}][tf_items][${i}][is_correct]`" form="createExamForm" :id="`q${index}_opt${i}_true`" value="1" x-model="item.is_correct"><label class="btn btn-outline-success btn-sm" :for="`q${index}_opt${i}_true`">Đúng</label></div></div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-white w-100 py-3 text-primary fw-bold border-dashed" style="border: 2px dashed #a5b4fc;" @click="newQuestions.push({ grade: '10', orientation: 'chung', topic_id: '', competency_id: '', core_content: '', yccd: '', level: 'medium', type: 'single_choice', content: '', options: ['', '', '', ''], correct_index: 0, tf_items: [{content: '', is_correct: 0}, {content: '', is_correct: 0}, {content: '', is_correct: 0}, {content: '', is_correct: 0}] });"><i class="bi bi-plus-circle-fill me-2"></i> THÊM CÂU HỎI MỚI</button>
                    </div>
                </div>
            </div>
    </div>

    {{-- [QUAN TRỌNG] FORM CHÍNH NẰM Ở CUỐI CÙNG, ẨN ĐI --}}
    <form id="createExamForm" action="{{ route('teacher.exams.store') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="question_ids" id="finalQuestionIds">
    </form>

    @push('scripts')
    <div class="modal fade" id="questionPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-4" id="previewContent"></div>
                <div class="modal-footer bg-light border-0 py-2"><button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng</button></div>
            </div>
        </div>
    </div>
    <script>
        function showQuestionPreview(button) {
            const q = JSON.parse(button.getAttribute('data-question'));
            const modalBody = document.getElementById('previewContent');
            let html = `<div class="p-4 bg-light rounded-3 border mb-4"><h6 class="fw-bold text-primary mb-3"><i class="bi bi-question-circle-fill me-2"></i> Nội dung câu hỏi:</h6><div class="fs-5 text-dark" style="line-height: 1.6;">${q.content}</div></div>`;
            if (q.type === 'single_choice' && q.answers) {
                html += '<div class="list-group shadow-sm">';
                q.answers.forEach((ans) => {
                    let itemClass = ans.is_correct == 1 ? 'list-group-item-success fw-bold border-success' : 'list-group-item-light border-0 mb-1';
                    let icon = ans.is_correct == 1 ? '<i class="bi bi-check-circle-fill text-success me-2"></i>' : '<i class="bi bi-circle text-muted me-2"></i>';
                    html += `<div class="list-group-item ${itemClass} p-3 d-flex align-items-center">${icon} ${ans.content}</div>`;
                });
                html += '</div>';
            } else if (q.type === 'true_false_group' && q.children) {
                html += '<div class="table-responsive rounded-3 shadow-sm border"><table class="table table-striped mb-0 align-middle"><thead class="bg-primary text-white"><tr><th class="p-3">Ý nhận định</th><th class="text-center p-3" width="120">Đáp án</th></tr></thead><tbody>';
                q.children.forEach(child => {
                    let correctAns = child.answers.find(a => a.is_correct == 1);
                    let resultText = correctAns ? correctAns.content : 'N/A';
                    let badgeClass = resultText === 'Đúng' ? 'bg-success' : 'bg-danger';
                    html += `<tr><td class="p-3">${child.content}</td><td class="text-center p-3"><span class="badge ${badgeClass} rounded-pill px-3 py-2 w-75">${resultText}</span></td></tr>`;
                });
                html += '</tbody></table></div>';
            }
            modalBody.innerHTML = html;
            new bootstrap.Modal(document.getElementById('questionPreviewModal')).show();
        }
    </script>
    @endpush

</x-layouts.teacher>