<x-layouts.teacher title="Tạo đề thi & Chọn câu hỏi">
    
    <div class="container-fluid p-0">
        <h3 class="mb-4 text-primary fw-bold">Tạo đề thi & Chọn câu hỏi</h3>

        {{-- 1. BỘ LỌC CÂU HỎI --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-white fw-bold border-bottom">
                <i class="bi bi-funnel"></i> 1. Bộ lọc câu hỏi
            </div>
            <div class="card-body bg-light">
                <form action="{{ route('teacher.exams.create') }}" method="GET">
                    <div class="row g-3">
                        {{-- Hàng 1: Các bộ lọc ngắn --}}
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Lớp</label>
                            <select name="grade" class="form-select form-select-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="10" {{ request('grade') == '10' ? 'selected' : '' }}>Lớp 10</option>
                                <option value="11" {{ request('grade') == '11' ? 'selected' : '' }}>Lớp 11</option>
                                <option value="12" {{ request('grade') == '12' ? 'selected' : '' }}>Lớp 12</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Chủ đề</label>
                            <select name="topic_id" class="form-select form-select-sm">
                                <option value="">-- Tất cả --</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                                        {{ $topic->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Định hướng</label>
                            <select name="orientation" class="form-select form-select-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="chung" {{ request('orientation') == 'chung' ? 'selected' : '' }}>Chung</option>
                                <option value="ict" {{ request('orientation') == 'ict' ? 'selected' : '' }}>ICT</option>
                                <option value="cs" {{ request('orientation') == 'cs' ? 'selected' : '' }}>CS</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Dạng câu hỏi</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="single_choice" {{ request('type') == 'single_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                                <option value="true_false_group" {{ request('type') == 'true_false_group' ? 'selected' : '' }}>Đúng/Sai chùm</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Mức độ</label>
                            <select name="cognitive_level_id" class="form-select form-select-sm">
                                <option value="">-- Tất cả --</option>
                                @foreach($levels as $lv)
                                    <option value="{{ $lv->id }}" {{ request('cognitive_level_id') == $lv->id ? 'selected' : '' }}>
                                        {{ $lv->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Hàng 2: Bộ lọc Năng lực (Mới bổ sung) --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small">Năng lực</label>
                            <select name="competency_id" class="form-select form-select-sm">
                                <option value="">-- Tất cả năng lực --</option>
                                @foreach($competencies as $comp)
                                    <option value="{{ $comp->id }}" {{ request('competency_id') == $comp->id ? 'selected' : '' }}>
                                        {{ $comp->code }}: {{ $comp->description }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Nút bấm --}}
                        <div class="col-12 text-end border-top pt-3 mt-3">
                            <a href="{{ route('teacher.exams.create') }}" class="btn btn-secondary btn-sm me-2">Đặt lại bộ lọc</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">🔍 Tìm kiếm câu hỏi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 2. KHU VỰC CHỌN CÂU HỎI VÀ TẠO ĐỀ --}}
        <form action="{{ route('teacher.exams.store') }}" method="POST" id="createExamForm">
            @csrf
            
            <div class="row">
                {{-- Cột Trái: Danh sách câu hỏi --}}
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <span class="fw-bold text-primary">
                                📚 Danh sách câu hỏi ({{ $questions->total() }})
                            </span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                                <label class="form-check-label small fw-bold" for="checkAll">Chọn tất cả trang này</label>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50" class="text-center">#</th>
                                            <th>Nội dung</th>
                                            <th width="150">Thông tin</th>
                                            <th width="80">Chi tiết</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($questions as $q)
                                            <tr>
                                                <td class="text-center">
                                                    <input class="form-check-input question-checkbox" 
                                                           type="checkbox" 
                                                           value="{{ $q->id }}" 
                                                           style="transform: scale(1.3); cursor: pointer;"
                                                           onchange="toggleQuestion('{{ $q->id }}', this.checked)">
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark text-truncate" style="max-width: 450px;" title="{{ $q->content }}">
                                                        {{ Str::limit($q->content, 100) }}
                                                    </div>
                                                    {{-- Hiển thị thêm thông tin năng lực nếu có --}}
                                                    @if($q->competency)
                                                        <div class="small text-muted fst-italic mt-1">
                                                            <i class="bi bi-lightning-charge"></i> {{ $q->competency->code }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">{{ $q->grade }}</span>
                                                    <span class="badge {{ $q->type == 'single_choice' ? 'bg-info' : 'bg-warning' }} bg-opacity-75 text-white">
                                                        {{ $q->type == 'single_choice' ? 'TN' : 'Đ/S' }}
                                                    </span>
                                                    <div class="small text-muted mt-1">{{ $q->topic->name ?? 'N/A' }}</div>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary">Xem</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted">
                                                    Không tìm thấy câu hỏi nào phù hợp với bộ lọc.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            {{ $questions->withQueryString()->links() }} 
                        </div>
                    </div>
                </div>

                {{-- Cột Phải: Form thông tin đề thi (Sticky) --}}
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 sticky-top" style="top: 20px; z-index: 99;">
                        <div class="card-header bg-primary text-white fw-bold py-3">
                            📝 Thông tin đề thi
                        </div>
                        <div class="card-body">
                            {{-- Tên đề --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên đề thi <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="VD: Kiểm tra 15 phút Tin học 11" required>
                            </div>
                            
                            {{-- Thời gian --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời gian (phút) <span class="text-danger">*</span></label>
                                <input type="number" name="duration" class="form-control" value="45" min="5" required>
                            </div>

                            {{-- Mật khẩu đề mẫu --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Mật khẩu đề mẫu</label>
                                <input type="text" name="password" class="form-control" placeholder="Bỏ trống nếu công khai">
                                <div class="form-text text-muted small">Dùng cho giáo viên khác khi copy đề.</div>
                            </div>

                            {{-- Hiển thị số lượng đã chọn --}}
                            <div class="alert alert-warning d-flex align-items-center mb-3">
                                <h2 class="mb-0 me-3 fw-bold text-primary" id="countSelected" style="font-size: 2rem;">0</h2>
                                <div>câu hỏi<br>đã được chọn</div>
                            </div>

                            {{-- Input ẩn chứa danh sách ID --}}
                            <input type="hidden" name="question_ids" id="finalQuestionIds">

                            <button type="button" onclick="submitExamForm()" class="btn btn-success w-100 fw-bold py-3 shadow-sm">
                                <i class="bi bi-check-circle-fill"></i> HOÀN TẤT TẠO ĐỀ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Script xử lý LocalStorage --}}
    <script>
        // 1. Khởi tạo
        let selectedQuestions = JSON.parse(localStorage.getItem('exam_cart')) || [];

        // 2. Chạy khi load trang
        document.addEventListener("DOMContentLoaded", function() {
            updateUI();
            
            // Tích vào các checkbox đã được lưu
            document.querySelectorAll('.question-checkbox').forEach(cb => {
                if (selectedQuestions.includes(cb.value)) {
                    cb.checked = true;
                }
            });
        });

        // 3. Xử lý tích chọn từng cái
        function toggleQuestion(id, isChecked) {
            id = String(id);
            if (isChecked) {
                if (!selectedQuestions.includes(id)) selectedQuestions.push(id);
            } else {
                selectedQuestions = selectedQuestions.filter(item => item !== id);
            }
            saveToStorage();
        }

        // 4. Chọn tất cả
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

        // 5. Lưu và cập nhật UI
        function saveToStorage() {
            localStorage.setItem('exam_cart', JSON.stringify(selectedQuestions));
            updateUI();
        }

        function updateUI() {
            const countSpan = document.getElementById('countSelected');
            if(countSpan) countSpan.innerText = selectedQuestions.length;
        }

        // 6. Submit Form
        function submitExamForm() {
            if (selectedQuestions.length === 0) {
                alert("Bạn chưa chọn câu hỏi nào!");
                return;
            }
            document.getElementById('finalQuestionIds').value = selectedQuestions.join(',');
            localStorage.removeItem('exam_cart'); 
            document.getElementById('createExamForm').submit();
        }
    </script>

</x-layouts.teacher>