<x-layouts.teacher title="Chỉnh sửa đề thi">

    @push('styles')
    <style>
        /* (Giữ nguyên style như file create) */
        .card-header-custom { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; }
        .filter-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; }
        .question-checkbox { width: 1.2em; height: 1.2em; cursor: pointer; border-color: #cbd5e1; }
        .question-checkbox:checked { background-color: #4f46e5; border-color: #4f46e5; }
        .sticky-sidebar { top: 100px; z-index: 99; }
        .badge-type-sc { background-color: #0ea5e9; }
        .badge-type-tf { background-color: #f59e0b; }
    </style>
    @endpush

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}" class="text-muted">Đề thi</a></li>
            <li class="breadcrumb-item active text-primary fw-bold">Chỉnh sửa: #{{ $exam->id }}</li>
        </ol>
    </nav>

    <div class="container-fluid p-0">
        
        {{-- 1. BỘ LỌC (Action trỏ về trang EDIT hiện tại để giữ ID) --}}
        <div class="card shadow-sm mb-4 border-0 rounded-4">
            <div class="card-header bg-white fw-bold border-bottom py-3">
                <i class="bi bi-funnel-fill text-primary me-2"></i> Thêm câu hỏi vào đề
            </div>
            <div class="card-body filter-box m-3">
                <form action="{{ route('teacher.exams.edit', $exam->id) }}" method="GET">
                    <div class="row g-3">
                        {{-- (Các ô select giữ nguyên như file create, chỉ cần thêm selected state nếu muốn) --}}
                        <div class="col-md-2">
                            <select name="grade" class="form-select form-select-sm shadow-sm">
                                <option value="">-- Lớp --</option>
                                <option value="10" {{ request('grade') == '10' ? 'selected' : '' }}>Lớp 10</option>
                                <option value="11" {{ request('grade') == '11' ? 'selected' : '' }}>Lớp 11</option>
                                <option value="12" {{ request('grade') == '12' ? 'selected' : '' }}>Lớp 12</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="topic_id" class="form-select form-select-sm shadow-sm">
                                <option value="">-- Chủ đề --</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- ... Các bộ lọc khác tương tự ... --}}
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm">
                                <i class="bi bi-search me-1"></i> Lọc
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 2. FORM CHỈNH SỬA --}}
        <form action="{{ route('teacher.exams.update', $exam->id) }}" method="POST" id="editExamForm">
            @csrf
            @method('PUT') {{-- Quan trọng cho Update --}}
            
            <div class="row g-4">
                {{-- Cột Trái: Danh sách câu hỏi để chọn thêm --}}
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 rounded-4 mb-5">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-list-task text-primary me-2"></i> Kết quả tìm kiếm
                            </h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light small text-uppercase">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Nội dung</th>
                                        <th>Phân loại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($questions as $q)
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input question-checkbox" 
                                                       type="checkbox" 
                                                       value="{{ $q->id }}" 
                                                       onchange="toggleQuestion('{{ $q->id }}', this.checked)">
                                            </td>
                                            <td>
                                                <div class="fw-bold text-truncate" style="max-width: 450px;">{{ Str::limit($q->content, 100) }}</div>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">Lớp {{ $q->grade }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white border-top-0 py-3">
                            {{ $questions->withQueryString()->links() }}
                        </div>
                    </div>
                </div>

                {{-- Cột Phải: Thông tin đề thi (Sticky) --}}
                <div class="col-md-4">
                    <div class="card shadow-lg border-0 rounded-4 sticky-sidebar">
                        <div class="card-header card-header-custom text-white fw-bold py-3">
                            <i class="bi bi-pencil-square me-2"></i> Cập nhật thông tin
                        </div>
                        <div class="card-body p-4">
                            {{-- Tên đề (Có value cũ) --}}
                            <div class="mb-3">
                                <label class="fw-bold">Tên đề thi <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ $exam->title }}" required>
                            </div>
                            
                            {{-- Thời gian (Có value cũ) --}}
                            <div class="mb-3">
                                <label class="fw-bold">Thời gian (phút)</label>
                                <input type="number" name="duration" class="form-control" value="{{ $exam->duration }}" required>
                            </div>

                            {{-- Trạng thái Public/Private (Có checked cũ) --}}
                            <div class="mb-4">
                                <div class="card p-3 border bg-light">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_public" id="isPublicSwitch" value="1" 
                                            {{ $exam->is_public ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="isPublicSwitch">Công khai</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Số lượng đã chọn --}}
                            <div class="d-flex justify-content-between p-3 rounded-3 mb-3 bg-primary bg-opacity-10 border border-primary">
                                <div>
                                    <div class="small text-uppercase fw-bold">Tổng câu hỏi</div>
                                    <div class="h3 fw-bold text-primary mb-0" id="countSelected">0</div>
                                </div>
                                <i class="bi bi-layers-fill text-primary fs-1 opacity-50"></i>
                            </div>

                            <input type="hidden" name="question_ids" id="finalQuestionIds">

                            <button type="button" onclick="submitEditForm()" class="btn btn-primary w-100 fw-bold py-3 shadow">
                                <i class="bi bi-save me-1"></i> LƯU THAY ĐỔI
                            </button>
                            <a href="{{ route('teacher.exams.index') }}" class="btn btn-light w-100 mt-2">Hủy bỏ</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // --- KHỞI TẠO DỮ LIỆU TỪ SERVER (KHÔNG DÙNG LOCALSTORAGE) ---
        // Blade sẽ render mảng ID PHP thành mảng Javascript
        let selectedQuestions = @json($currentQuestionIds);
        
        // Chuyển tất cả ID sang string để so sánh nhất quán
        selectedQuestions = selectedQuestions.map(String);

        document.addEventListener("DOMContentLoaded", function() {
            updateUI();
            
            // Đánh dấu các checkbox đang có trong danh sách
            document.querySelectorAll('.question-checkbox').forEach(cb => {
                if (selectedQuestions.includes(String(cb.value))) {
                    cb.checked = true;
                }
            });
        });

        function toggleQuestion(id, isChecked) {
            id = String(id);
            if (isChecked) {
                if (!selectedQuestions.includes(id)) selectedQuestions.push(id);
            } else {
                selectedQuestions = selectedQuestions.filter(item => item !== id);
            }
            updateUI();
        }

        function updateUI() {
            document.getElementById('countSelected').innerText = selectedQuestions.length;
        }

        function submitEditForm() {
            if (selectedQuestions.length === 0) {
                alert("Đề thi phải có ít nhất 1 câu hỏi!");
                return;
            }
            document.getElementById('finalQuestionIds').value = selectedQuestions.join(',');
            document.getElementById('editExamForm').submit();
        }
    </script>
    @endpush

</x-layouts.teacher>