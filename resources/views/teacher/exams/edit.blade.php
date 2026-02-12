<x-layouts.shared title="Chỉnh sửa đề thi #{{ $exam->id }}">
    @push('styles')
    <style>
        .card-header-custom { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; }
        .sticky-sidebar { position: sticky; top: 10px; z-index: 100; }
        .q-item { transition: all 0.2s; border-left: 5px solid transparent; background: #fff; }
        .q-item:hover { background-color: #f8fafc; }
        .q-item.selected { border-left-color: #4f46e5; }
        .answer-list { display: none; }
        .cursor-pointer { cursor: pointer; }
        .chevron-icon { transition: transform 0.3s ease; }
        .rotate-180 { transform: rotate(180deg); }
        .ans-row { padding: 10px 15px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 8px; display: flex; align-items: center; font-size: 0.9rem; }
        .ans-correct { background-color: #f0fdf4; border-color: #86efac; color: #166534; font-weight: 600; }
        .border-dashed { border-style: dashed !important; }
        .drag-handle { cursor: grab; padding: 5px; color: #cbd5e1; transition: color 0.2s; }
        .drag-handle:hover { color: #4f46e5; }
        .drag-handle:active { cursor: grabbing; }
        .sortable-ghost { opacity: 0.4; background-color: #f1f5f9 !important; border: 2px dashed #4f46e5 !important; }
        [x-cloak] { display: none !important; }
    </style>
    @endpush

    <div class="container-fluid p-0" x-data="examEditor()" x-init="init()" x-cloak>
        <div class="row g-4">
            {{-- CỘT TRÁI: QUẢN LÝ CÂU HỎI TRONG ĐỀ --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5 text-dark">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-list-ol me-2"></i>Danh sách câu hỏi (<span x-text="bankQuestions.length"></span>)
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        {{-- Form chính dùng để lưu --}}
                        <form id="editExamForm" action="{{ route('teacher.exams.update', $exam->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="question_ids" :value="bankQuestions.map(q => q.id).join(',')">
                            
                            {{-- Input ẩn để gửi nội dung sửa đổi --}}
                            <template x-for="q in bankQuestions" :key="'hidden-'+q.id">
                                <div>
                                    <input type="hidden" :name="'edited_contents['+q.id+']'" :value="q.content">
                                    <input type="hidden" :name="'edited_answers['+q.id+']'" :value="JSON.stringify(q.answers)">
                                </div>
                            </template>

                            <div x-ref="sortableContainer">
                                <template x-if="bankQuestions.length === 0">
                                    <div class="text-center py-5 text-muted border rounded-3 border-dashed">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                        Chưa có câu hỏi nào trong đề thi này.
                                    </div>
                                </template>
                                
                                <template x-for="(q, index) in bankQuestions" :key="q.id">
                                    <div class="p-3 mb-3 border rounded-3 q-item selected shadow-sm bg-white" :data-id="q.id">
                                        <div class="d-flex justify-content-between align-items-start">
                                            {{-- Tay cầm kéo thả --}}
                                            <div class="drag-handle me-2" title="Kéo để sắp xếp"><i class="bi bi-grip-vertical fs-5"></i></div>
                                            
                                            <div class="flex-grow-1 pe-3">
                                                {{-- Chế độ Xem --}}
                                                <div x-show="!q.isEditing" @click="toggleAccordion(q.id)" class="cursor-pointer">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge bg-primary me-2" x-text="index + 1"></span>
                                                        <span class="badge border small text-white" :class="getLevelClass(q.level)" x-text="getLevelName(q.level)"></span>
                                                        <i class="bi bi-chevron-down ms-2 small text-muted chevron-icon" :id="'icon-' + q.id"></i>
                                                    </div>
                                                    <div class="text-dark fw-bold d-inline ms-1" x-html="limitText(q.content, 150)"></div>
                                                </div>
                                                
                                                {{-- Chế độ Sửa nội dung câu hỏi --}}
                                                <div x-show="q.isEditing" class="mt-2">
                                                    <label class="small fw-bold text-muted mb-1">Nội dung câu hỏi</label>
                                                    <textarea class="form-control mb-2 fw-bold shadow-none" rows="3" x-model="q.content"></textarea>
                                                </div>
                                            </div>

                                            {{-- Nút thao tác --}}
                                            <div class="d-flex gap-1">
                                                <button type="button" @click="q.isEditing = !q.isEditing" class="btn btn-sm btn-outline-primary border-0 shadow-none" title="Chỉnh sửa">
                                                    <i class="bi" :class="q.isEditing ? 'bi-check-lg' : 'bi-pencil-square'"></i>
                                                </button>
                                                <button type="button" @click="removeQuestion(q.id)" class="btn btn-sm btn-outline-danger border-0 shadow-none" title="Xóa khỏi đề">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Chi tiết đáp án (Accordion) --}}
                                        <div :id="'ans-' + q.id" class="answer-list mt-3" :style="q.isEditing ? 'display: block' : ''">
                                            <div class="p-3 bg-light rounded-3 border text-dark shadow-inner small">
                                                <div class="fw-bold text-primary mb-2 text-uppercase">Đáp án chi tiết:</div>
                                                <div class="bg-white p-2 border rounded">
                                                    
                                                    {{-- Trường hợp Trắc nghiệm 1 lựa chọn --}}
                                                    <template x-if="q.q_type === 'single_choice'">
                                                        <div class="d-flex flex-column gap-2">
                                                            <template x-for="(ans, i) in q.answers" :key="i">
                                                                <div>
                                                                    {{-- Xem đáp án --}}
                                                                    <div x-show="!q.isEditing" class="ans-row border" :class="ans.is_correct ? 'ans-correct' : ''">
                                                                        <span class="fw-bold me-2" x-text="String.fromCharCode(65 + i) + '.'"></span>
                                                                        <span x-text="ans.content"></span>
                                                                        <template x-if="ans.is_correct"><i class="bi bi-check-circle-fill ms-auto text-success"></i></template>
                                                                    </div>
                                                                    {{-- Sửa đáp án --}}
                                                                    <div x-show="q.isEditing" class="input-group input-group-sm mb-1">
                                                                        <span class="input-group-text fw-bold" x-text="String.fromCharCode(65 + i)"></span>
                                                                        <input type="text" class="form-control" x-model="ans.content">
                                                                        <div class="input-group-text bg-white">
                                                                            <input class="form-check-input mt-0" type="radio" :name="'correct_radio_' + q.id" :checked="ans.is_correct" @change="q.answers.forEach((a, idx) => a.is_correct = (idx === i))">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    {{-- Trường hợp Đúng/Sai --}}
                                                    <template x-if="q.q_type === 'true_false_group'">
                                                        <table class="table table-sm table-bordered bg-white mb-0 align-middle">
                                                            <thead class="table-secondary small text-center">
                                                                <tr><th>Ý nhận định</th><th width="80">Đáp án</th></tr>
                                                            </thead>
                                                            <tbody>
                                                                <template x-for="(ans, i) in q.answers" :key="i">
                                                                    <tr>
                                                                        <td>
                                                                            <div x-show="!q.isEditing" class="p-1" x-text="ans.content"></div>
                                                                            <textarea x-show="q.isEditing" class="form-control form-control-sm border-0" x-model="ans.content" rows="2"></textarea>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <div x-show="!q.isEditing" class="fw-bold text-primary" x-text="ans.is_correct ? 'Đúng' : 'Sai'"></div>
                                                                            <select x-show="q.isEditing" class="form-select form-select-sm border-0 fw-bold text-primary" x-model="ans.is_correct">
                                                                                <option :value="true">Đúng</option>
                                                                                <option :value="false">Sai</option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                </template>
                                                            </tbody>
                                                        </table>
                                                    </template>

                                                </div>
                                                {{-- Nút Xong khi sửa --}}
                                                <div x-show="q.isEditing" class="mt-2 text-end">
                                                    <button type="button" @click="q.isEditing = false" class="btn btn-sm btn-primary px-3 shadow-sm">Hoàn tất</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: INFO & SIDEBAR --}}
            <div class="col-md-4">
                <div class="card shadow-lg border-0 rounded-4 sticky-sidebar text-dark">
                    <div class="card-header card-header-custom p-3 rounded-top-4">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Thông tin đề thi</h6>
                    </div>
                    <div class="card-body p-4">
                        {{-- Tiêu đề --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tiêu đề đề thi</label>
                            <input type="text" form="editExamForm" name="title" class="form-control fw-bold shadow-none" value="{{ $exam->title }}" required>
                        </div>

                        {{-- [MỚI] Phần Mô tả đề thi --}}
                        <div class="mb-3" x-data="{ desc: @js($exam->description ?? '') }">
                            <label class="form-label small fw-bold text-muted text-uppercase d-flex justify-content-between">
                                Mô tả / Ghi chú
                                <span class="fw-normal text-muted" style="font-size: 0.7em" x-text="(desc ? desc.length : 0) + '/255'"></span>
                            </label>
                            <textarea form="editExamForm" name="description" class="form-control shadow-none text-secondary" rows="3" maxlength="255" x-model="desc" placeholder="Nhập ghi chú hoặc hướng dẫn làm bài..."></textarea>
                        </div>

                        {{-- Thời gian & Trạng thái --}}
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <label class="small fw-bold text-muted">THỜI GIAN (PHÚT)</label>
                                <input type="number" form="editExamForm" name="duration" class="form-control shadow-none" value="{{ $exam->duration }}" required>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted">TRẠNG THÁI</label>
                                <select form="editExamForm" name="is_public" class="form-select small shadow-none">
                                    <option value="1" {{ $exam->is_public ? 'selected' : '' }}>Công khai</option>
                                    <option value="0" {{ !$exam->is_public ? 'selected' : '' }}>Nháp</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Ma trận --}}
                        <div class="p-3 bg-light border border-dashed rounded-3 mb-4 shadow-none">
                            <h6 class="fw-bold small border-bottom pb-2 mb-3 text-uppercase">Thống kê</h6>
                            <div class="d-flex flex-column gap-2 small">
                                <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                    <span>Tổng số câu:</span><strong class="text-primary h5 mb-0" x-text="bankQuestions.length"></strong>
                                </div>
                                <div class="d-flex justify-content-between"><span>Nhận biết:</span><strong class="text-success" x-text="countLevel('easy')"></strong></div>
                                <div class="d-flex justify-content-between"><span>Thông hiểu:</span><strong class="text-primary" x-text="countLevel('medium')"></strong></div>
                                <div class="d-flex justify-content-between"><span>Vận dụng:</span><strong class="text-warning" x-text="countLevel('hard')"></strong></div>
                            </div>
                        </div>
                        
                        <button type="submit" form="editExamForm" class="btn btn-primary w-100 fw-bold py-3 shadow-lg rounded-3">CẬP NHẬT ĐỀ THI</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        function examEditor() {
            return {
                // Chỉ lấy dữ liệu câu hỏi đã chọn trong đề
                bankQuestions: @json($selectedQuestions).map(q => ({...q, isEditing: false})),
                sortableInstance: null,

                init() {
                    this.setupSortable();
                },

                setupSortable() {
                    this.$nextTick(() => {
                        const container = this.$refs.sortableContainer;
                        if (!container) return;
                        if (this.sortableInstance) this.sortableInstance.destroy();

                        this.sortableInstance = Sortable.create(container, {
                            handle: '.drag-handle',
                            animation: 200,
                            ghostClass: 'sortable-ghost',
                            onEnd: (evt) => {
                                const oldIndex = evt.oldIndex;
                                const newIndex = evt.newIndex;
                                if (oldIndex === newIndex) return;

                                const parent = evt.from;
                                const item = evt.item;
                                if (newIndex > oldIndex) { parent.insertBefore(item, parent.children[oldIndex]); } 
                                else { parent.insertBefore(item, parent.children[oldIndex + 1]); }

                                const items = [...this.bankQuestions];
                                const movedItem = items.splice(oldIndex, 1)[0];
                                items.splice(newIndex, 0, movedItem);
                                this.bankQuestions = items;
                            }
                        });
                    });
                },

                removeQuestion(id) {
                    if(confirm('Bạn có chắc muốn xóa câu hỏi này khỏi đề thi không?')) {
                        this.bankQuestions = this.bankQuestions.filter(q => q.id != String(id));
                    }
                },

                countLevel(lv) {
                    return this.bankQuestions.filter(q => q.level.toLowerCase() === lv.toLowerCase()).length;
                },

                getLevelName(level) {
                    const map = {'easy': 'Nhận biết', 'medium': 'Thông hiểu', 'hard': 'Vận dụng', 'very_hard': 'Vận dụng cao'};
                    return map[level.toLowerCase()] || level;
                },

                getLevelClass(level) {
                    const map = {'easy': 'bg-success', 'medium': 'bg-primary', 'hard': 'bg-warning text-dark', 'very_hard': 'bg-danger'};
                    return map[level.toLowerCase()] || 'bg-secondary';
                },

                limitText(text, limit) {
                    if(!text) return '';
                    let plainText = text.replace(/<[^>]*>/g, '');
                    return plainText.length > limit ? plainText.substring(0, limit) + '...' : plainText;
                },

                toggleAccordion(id) {
                    const el = document.getElementById('ans-' + id);
                    const icon = document.getElementById('icon-' + id);
                    if (el) {
                        const isVisible = el.style.display === 'block';
                        document.querySelectorAll('.answer-list').forEach(item => item.style.display = 'none');
                        document.querySelectorAll('.chevron-icon').forEach(i => i.classList.remove('rotate-180'));
                        el.style.display = isVisible ? 'none' : 'block';
                        if(!isVisible && icon) icon.classList.add('rotate-180');
                    }
                }
            }
        }
    </script>
    @endpush
</x-layouts.shared>