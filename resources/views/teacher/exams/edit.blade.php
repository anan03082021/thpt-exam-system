<x-layouts.teacher title="Chỉnh sửa đề thi #{{ $exam->id }}">

    @push('styles')
    <style>
        /* CSS đồng bộ với trang Create */
        .card-header-custom { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; }
        .filter-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; }
        .question-checkbox { width: 1.2em; height: 1.2em; cursor: pointer; border-color: #cbd5e1; }
        .question-checkbox:checked { background-color: #4f46e5; border-color: #4f46e5; }
        .sticky-sidebar { top: 100px; z-index: 99; }
        
        /* Badges */
        .badge-type-sc { background-color: #0ea5e9; } 
        .badge-type-tf { background-color: #f59e0b; }
        .badge-orient-chung { background-color: #64748b; }
        .badge-orient-cs { background-color: #4f46e5; }
        .badge-orient-ict { background-color: #059669; }
    </style>
    @endpush

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}" class="text-decoration-none text-muted">Đề thi</a></li>
            <li class="breadcrumb-item active text-primary fw-bold">Chỉnh sửa: {{ $exam->title }}</li>
        </ol>
    </nav>

    <div class="container-fluid p-0">
        
        {{-- 1. BỘ LỌC CÂU HỎI --}}
        <div class="card shadow-sm mb-4 border-0 rounded-4">
            <div class="card-header bg-white fw-bold border-bottom py-3">
                <i class="bi bi-funnel-fill text-primary me-2"></i> Bộ lọc câu hỏi thêm
            </div>
            <div class="card-body filter-box m-3">
                <form action="{{ route('teacher.exams.edit', $exam->id) }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-bold">Lớp</label>
                            <select name="grade" class="form-select form-select-sm border-0 shadow-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="10" {{ request('grade') == '10' ? 'selected' : '' }}>Lớp 10</option>
                                <option value="11" {{ request('grade') == '11' ? 'selected' : '' }}>Lớp 11</option>
                                <option value="12" {{ request('grade') == '12' ? 'selected' : '' }}>Lớp 12</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold">Chủ đề</label>
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
                            <label class="form-label small text-muted fw-bold">Định hướng</label>
                            <select name="orientation" class="form-select form-select-sm border-0 shadow-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="chung" {{ request('orientation') == 'chung' ? 'selected' : '' }}>Chung</option>
                                <option value="cs" {{ request('orientation') == 'cs' ? 'selected' : '' }}>CS</option>
                                <option value="ict" {{ request('orientation') == 'ict' ? 'selected' : '' }}>ICT</option>
                            </select>
                        </div>
                        {{-- Các bộ lọc khác giữ nguyên nếu cần --}}
                        
                        <div class="col-12 text-end pt-2">
                            <a href="{{ route('teacher.exams.edit', $exam->id) }}" class="btn btn-light btn-sm me-2">Đặt lại</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">
                                <i class="bi bi-search me-1"></i> Tìm kiếm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 2. FORM SỬA ĐỀ --}}
        <form action="{{ route('teacher.exams.update', $exam->id) }}" method="POST" id="editExamForm">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                {{-- Cột Trái: Danh sách câu hỏi --}}
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 rounded-4 mb-5">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-list-task text-primary me-2"></i> Kết quả tìm kiếm
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
                                        <th>Nội dung</th>
                                        <th width="150">Phân loại</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($questions as $q)
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input question-checkbox" 
                                                       type="checkbox" 
                                                       value="{{ $q->id }}" 
                                                       data-type="{{ $q->orientation ?? 'chung' }}"
                                                       onchange="toggleQuestion('{{ $q->id }}', this.checked, '{{ $q->orientation ?? 'chung' }}')">
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 450px;">
                                                    {{ Str::limit($q->content, 100) }}
                                                </div>
                                                @if($q->competency)
                                                    <div class="small text-muted mt-1">
                                                        <i class="bi bi-lightning-charge-fill text-warning"></i> {{ $q->competency->code }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                {{-- Badge định hướng --}}
                                                @if($q->orientation == 'cs')
                                                    <span class="badge badge-orient-cs text-white border me-1">CS</span>
                                                @elseif($q->orientation == 'ict')
                                                    <span class="badge badge-orient-ict text-white border me-1">ICT</span>
                                                @else
                                                    <span class="badge badge-orient-chung text-white border me-1">Chung</span>
                                                @endif
                                                
                                                <span class="badge bg-light text-dark border">Lớp {{ $q->grade }}</span>
                                            </td>
                                            <td>
                                                {{-- Tìm đoạn nút bấm hình con mắt cũ và thay thế bằng đoạn này --}}
<button type="button" 
        class="btn btn-sm btn-outline-primary rounded-circle shadow-sm" 
        title="Xem chi tiết"
        data-question='@json($q)' 
        onclick="showQuestionPreview(this)">
    <i class="bi bi-eye"></i>
</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">Không tìm thấy câu hỏi nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white border-0 py-3">
                            {{ $questions->withQueryString()->links() }}
                        </div>
                    </div>
                </div>

                {{-- Cột Phải: Thông tin đề thi --}}
                <div class="col-md-4">
                    <div class="card shadow-lg border-0 rounded-4 sticky-sidebar">
                        <div class="card-header card-header-custom text-white fw-bold py-3 rounded-top-4">
                            <i class="bi bi-pencil-square me-2"></i> Thông tin đề thi
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên đề thi <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ $exam->title }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời gian (phút) <span class="text-danger">*</span></label>
                                <input type="number" name="duration" class="form-control" value="{{ $exam->duration }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Trạng thái phát hành</label>
                                <div class="card p-3 border shadow-sm bg-light">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_public" id="isPublicSwitch" value="1" 
                                            {{ $exam->is_public ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold ms-2" for="isPublicSwitch">Công khai</label>
                                    </div>
                                </div>
                            </div>

                            {{-- BỘ ĐẾM CHI TIẾT (ĐÃ NÂNG CẤP) --}}
                            <div class="p-3 rounded-3 mb-3 bg-white border border-primary border-opacity-25" style="border-style: dashed !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                                    <span class="fw-bold text-dark small text-uppercase">Tổng đã chọn</span>
                                    <span class="h4 fw-bold text-primary mb-0" id="totalCount">0</span>
                                </div>
                                <div class="small">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted"><i class="bi bi-layers me-1"></i> Phần Chung:</span>
                                        <span class="fw-bold text-dark" id="countChung">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-primary"><i class="bi bi-cpu me-1"></i> Phần CS:</span>
                                        <span class="fw-bold text-primary" id="countCS">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-success"><i class="bi bi-laptop me-1"></i> Phần ICT:</span>
                                        <span class="fw-bold text-success" id="countICT">0</span>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="question_ids" id="finalQuestionIds">

                            <button type="button" onclick="submitEditForm()" class="btn btn-primary w-100 fw-bold py-3 shadow-lg rounded-3">
                                <i class="bi bi-save me-1"></i> LƯU THAY ĐỔI
                            </button>
                            <a href="{{ route('teacher.exams.index') }}" class="btn btn-light w-100 mt-2 text-muted">Hủy bỏ</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    {{-- ======================================================= --}}
{{-- MODAL XEM NHANH CÂU HỎI --}}
{{-- ======================================================= --}}
<div class="modal fade" id="questionPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-search me-2"></i> Chi tiết câu hỏi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="previewContent">
                {{-- Nội dung sẽ được JS nạp vào đây --}}
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showQuestionPreview(button) {
        // 1. Lấy dữ liệu từ nút bấm
        const q = JSON.parse(button.getAttribute('data-question'));
        const modalBody = document.getElementById('previewContent');
        let html = '';

        // 2. Tạo Badge hiển thị loại câu hỏi
        let typeBadge = '';
        if (q.type === 'single_choice') typeBadge = '<span class="badge bg-info text-dark">Trắc nghiệm</span>';
        else typeBadge = '<span class="badge bg-warning text-dark">Đúng/Sai chùm</span>';

        // 3. Hiển thị nội dung câu hỏi gốc
        html += `
            <div class="d-flex justify-content-between align-items-center mb-3">
                ${typeBadge}
                <span class="badge bg-light text-secondary border">ID: ${q.id}</span>
            </div>
            <div class="p-3 bg-light rounded-3 border mb-4">
                <h6 class="fw-bold text-primary mb-2">Nội dung câu hỏi:</h6>
                <div class="fs-5">${q.content}</div>
            </div>
        `;

        // 4. Xử lý hiển thị đáp án theo loại
        html += '<h6 class="fw-bold text-dark mb-3">Đáp án / Câu hỏi thành phần:</h6>';

        // --- TRƯỜNG HỢP A: TRẮC NGHIỆM ---
        if (q.type === 'single_choice' && q.answers) {
            html += '<div class="list-group">';
            q.answers.forEach((ans, index) => {
                // Tô màu xanh nếu là đáp án đúng
                let itemClass = ans.is_correct == 1 
                    ? 'list-group-item-success fw-bold border-success' 
                    : 'list-group-item-light';
                
                let icon = ans.is_correct == 1 
                    ? '<i class="bi bi-check-circle-fill text-success me-2"></i>' 
                    : '<i class="bi bi-circle text-muted me-2"></i>';

                html += `
                    <div class="list-group-item ${itemClass}">
                        ${icon} ${ans.content}
                    </div>
                `;
            });
            html += '</div>';
        } 
        
        // --- TRƯỜNG HỢP B: ĐÚNG / SAI CHÙM ---
        else if (q.type === 'true_false_group' && q.children) {
            html += '<div class="table-responsive"><table class="table table-bordered align-middle">';
            html += '<thead class="table-light"><tr><th>Ý nhận định</th><th class="text-center" width="100">Đáp án</th></tr></thead><tbody>';
            
            q.children.forEach(child => {
                // Tìm đáp án đúng của câu con này
                let correctAns = child.answers.find(a => a.is_correct == 1);
                let resultText = correctAns ? correctAns.content : 'N/A';
                let badgeClass = resultText === 'Đúng' ? 'bg-success' : 'bg-danger';

                html += `
                    <tr>
                        <td>${child.content}</td>
                        <td class="text-center">
                            <span class="badge ${badgeClass}">${resultText}</span>
                        </td>
                    </tr>
                `;
            });
            html += '</tbody></table></div>';
        }

        // 5. Hiển thị Modal
        modalBody.innerHTML = html;
        var myModal = new bootstrap.Modal(document.getElementById('questionPreviewModal'));
        myModal.show();
    }
</script>
    <script>
        // --- 1. KHỞI TẠO DỮ LIỆU ---
        // Lấy dữ liệu từ Controller truyền sang (Biến $selectedQuestionsInit)
        // Nếu Controller chưa truyền thì fallback về mảng rỗng để tránh lỗi
        let dbQuestions = @json($selectedQuestionsInit ?? []); 
        
        // Key lưu trữ riêng cho trang Edit (để không bị lẫn với trang Create)
        let storageKey = 'exam_edit_cart_' + {{ $exam->id }};
        
        // Ưu tiên lấy từ LocalStorage (nếu đang sửa dở), nếu không thì lấy từ DB
        let selectedQuestions = JSON.parse(localStorage.getItem(storageKey));

        if (!selectedQuestions || selectedQuestions.length === 0) {
            // Chuẩn hóa dữ liệu từ DB (đảm bảo type luôn có giá trị)
            selectedQuestions = dbQuestions.map(item => ({
                id: String(item.id),
                type: (item.type || 'chung').toLowerCase()
            }));
            localStorage.setItem(storageKey, JSON.stringify(selectedQuestions));
        }

        document.addEventListener("DOMContentLoaded", function() {
            updateUI();
            
            // Đánh dấu các checkbox
            let savedIds = selectedQuestions.map(item => item.id);
            document.querySelectorAll('.question-checkbox').forEach(cb => {
                if (savedIds.includes(String(cb.value))) {
                    cb.checked = true;
                }
            });
        });

        // --- 2. HÀM TOGGLE (Lưu cả ID và Type) ---
        function toggleQuestion(id, isChecked, type = 'chung') {
            id = String(id);
            type = type.toLowerCase();
            
            if (isChecked) {
                if (!selectedQuestions.some(item => item.id === id)) {
                    selectedQuestions.push({ id: id, type: type });
                }
            } else {
                selectedQuestions = selectedQuestions.filter(item => item.id !== id);
            }
            saveToStorage();
        }

        // --- 3. CẬP NHẬT GIAO DIỆN (Đếm chi tiết) ---
        function updateUI() {
            document.getElementById('totalCount').innerText = selectedQuestions.length;
            
            let chung = selectedQuestions.filter(i => i.type == 'chung' || !i.type).length;
            let cs = selectedQuestions.filter(i => i.type == 'cs').length;
            let ict = selectedQuestions.filter(i => i.type == 'ict').length;

            document.getElementById('countChung').innerText = chung;
            document.getElementById('countCS').innerText = cs;
            document.getElementById('countICT').innerText = ict;
        }

        function saveToStorage() {
            localStorage.setItem(storageKey, JSON.stringify(selectedQuestions));
            updateUI();
        }

        // Check All
        const checkAllBox = document.getElementById('checkAll');
        if(checkAllBox) {
            checkAllBox.addEventListener('change', function() {
                let isChecked = this.checked;
                document.querySelectorAll('.question-checkbox').forEach(cb => {
                    cb.checked = isChecked;
                    let type = cb.getAttribute('data-type') || 'chung';
                    toggleQuestion(cb.value, isChecked, type);
                });
            });
        }

        // --- 4. SUBMIT FORM ---
        function submitEditForm() {
            if (selectedQuestions.length === 0) {
                alert("Đề thi phải có ít nhất 1 câu hỏi!");
                return;
            }
            // Chỉ gửi danh sách ID về server
            let ids = selectedQuestions.map(item => item.id);
            document.getElementById('finalQuestionIds').value = ids.join(',');
            
            // Xóa storage để lần sau vào lại sẽ load mới từ DB
            localStorage.removeItem(storageKey);
            
            document.getElementById('editExamForm').submit();
        }
    </script>
    @endpush

</x-layouts.teacher>