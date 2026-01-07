<x-layouts.teacher title="Ngân hàng câu hỏi">

    {{-- Hiển thị thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        {{-- Header --}}
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="bi bi-collection me-2"></i> Ngân hàng câu hỏi
            </h5>
            <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Thêm câu hỏi
            </a>
        </div>

        {{-- Bộ lọc (Optional - để sau này phát triển) --}}
        {{-- <div class="card-body border-bottom bg-light py-2"> ...Form tìm kiếm... </div> --}}

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4" width="5%">ID</th>
                        <th width="40%">Nội dung câu hỏi</th>
                        <th width="15%">Phân loại</th>
                        <th width="15%">Đặc điểm</th>
                        <th width="10%">Ngày tạo</th>
                        <th class="text-end pe-4" width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $q)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#{{ $q->id }}</td>
                            
                            {{-- Nội dung câu hỏi (Xử lý HTML từ CKEditor) --}}
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bold text-truncate" style="max-width: 450px;">
                                        {{-- strip_tags để loại bỏ thẻ HTML, hiển thị text thuần --}}
                                        {{ Str::limit(strip_tags($q->content), 80) }}
                                    </span>
                                    
                                    {{-- Hiển thị đáp án đúng (Gợi ý nhanh) --}}
                                    <small class="text-muted mt-1">
                                        @if($q->type == 'single_choice')
                                            @php $correct = $q->answers->where('is_correct', true)->first(); @endphp
                                            <i class="bi bi-check-circle-fill text-success me-1"></i> 
                                            Đáp án: {{ $correct ? Str::limit($correct->content, 30) : '(Chưa set)' }}
                                        @else
                                            <i class="bi bi-list-check text-primary me-1"></i> 
                                            {{ $q->children->count() }} ý nhận định
                                        @endif
                                    </small>
                                </div>
                            </td>

                            {{-- Loại & Lớp --}}
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    {{-- Badge Loại câu hỏi --}}
                                    @if($q->type == 'single_choice')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill w-auto align-self-start">
                                            Trắc nghiệm
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill w-auto align-self-start">
                                            Đúng/Sai
                                        </span>
                                    @endif

                                    {{-- Badge Lớp & Định hướng --}}
                                    <small class="text-muted">
                                        Lớp {{ $q->grade }} 
                                        @if($q->orientation) 
                                            &bull; {{ strtoupper($q->orientation) }} 
                                        @endif
                                    </small>
                                </div>
                            </td>

                            {{-- Chủ đề & Mức độ --}}
                            <td>
                                <div class="text-dark small fw-bold mb-1">{{ $q->topic->name ?? '---' }}</div>
                                @if($q->cognitiveLevel)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                        {{ $q->cognitiveLevel->name }}
                                    </span>
                                @endif
                            </td>

                            <td class="text-muted small">
                                {{ $q->created_at->format('d/m/Y') }}
                            </td>

                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Thao tác
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        {{-- Nút Xem Chi Tiết (Mở Modal) --}}
                                        <li>
                                            <button class="dropdown-item" onclick="showQuestionDetails({{ $q->id }})">
                                                <i class="bi bi-eye text-info me-2"></i> Xem chi tiết
                                            </button>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('teacher.questions.edit', $q->id) }}">
                                                <i class="bi bi-pencil-square text-warning me-2"></i> Chỉnh sửa
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('teacher.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa câu hỏi này không?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash me-2"></i> Xóa bỏ
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="opacity-25 mb-3">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                </div>
                                <h6 class="fw-bold text-secondary">Ngân hàng câu hỏi trống</h6>
                                <p class="text-muted small">Hãy bắt đầu tạo câu hỏi đầu tiên để xây dựng đề thi.</p>
                                <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary btn-sm px-4">
                                    Tạo mới ngay
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($questions->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $questions->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL XEM CHI TIẾT CÂU HỎI --}}
    <div class="modal fade" id="questionDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary">Chi tiết câu hỏi #<span id="modalQId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Nội dung câu hỏi (Render HTML) --}}
                    <div class="alert alert-light border mb-4">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Nội dung câu hỏi:</label>
                        <div id="modalQContent" class="text-dark"></div>
                    </div>

                    {{-- Danh sách đáp án --}}
                    <label class="fw-bold text-muted small text-uppercase mb-2">Phương án trả lời:</label>
                    <div id="modalAnswers" class="d-flex flex-column gap-2">
                        {{-- JS sẽ điền vào đây --}}
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Đóng</button>
                    <a href="#" id="modalEditBtn" class="btn btn-primary">Chỉnh sửa</a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Hàm hiển thị Modal Xem chi tiết
        // Lưu ý: Để tối ưu, ta nên dùng AJAX gọi lấy dữ liệu. 
        // Nhưng ở đây để đơn giản, ta truyền dữ liệu vào data-attribute của nút bấm hoặc lấy từ JSON array.
        
        // Cách tối ưu: Chuyển $questions sang JSON để JS truy cập
        const questionsData = @json($questions->items()); 

        function showQuestionDetails(id) {
            const question = questionsData.find(q => q.id === id);
            if(!question) return;

            document.getElementById('modalQId').innerText = question.id;
            document.getElementById('modalQContent').innerHTML = question.content; // Render HTML từ CKEditor
            document.getElementById('modalEditBtn').href = `/teacher/questions/${id}/edit`; // Cập nhật link nút sửa

            const answersDiv = document.getElementById('modalAnswers');
            answersDiv.innerHTML = '';

            // Xử lý hiển thị đáp án dựa theo loại câu hỏi
            if (question.type === 'single_choice') {
                if(question.answers) {
                    question.answers.forEach(ans => {
                        const isCorrect = ans.is_correct == 1;
                        const bgClass = isCorrect ? 'bg-success bg-opacity-10 border-success' : 'bg-white border';
                        const icon = isCorrect ? '<i class="bi bi-check-circle-fill text-success me-2"></i>' : '<i class="bi bi-circle text-muted me-2"></i>';
                        
                        answersDiv.innerHTML += `
                            <div class="p-3 rounded border ${bgClass} d-flex align-items-center">
                                ${icon} <span>${ans.content}</span>
                            </div>
                        `;
                    });
                }
            } else {
                // Đúng/Sai chùm (Cần load children - Lưu ý: trong index controller phải eager load 'children.answers')
                // Nếu JSON không có children đầy đủ, bạn cần bổ sung vào Controller: Question::with(['answers', 'children.answers', ...])
                if(question.children) {
                    question.children.forEach((child, index) => {
                        // Tìm đáp án đúng của câu con
                        let trueAns = child.answers.find(a => a.is_correct == 1);
                        let resultText = trueAns ? trueAns.content : 'Chưa set';
                        let badgeClass = resultText === 'Đúng' ? 'bg-success' : 'bg-danger';

                        answersDiv.innerHTML += `
                            <div class="p-3 rounded border bg-white mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Ý ${index + 1}: ${child.content}</span>
                                    <span class="badge ${badgeClass}">${resultText}</span>
                                </div>
                            </div>
                        `;
                    });
                }
            }

            // Mở Modal
            var myModal = new bootstrap.Modal(document.getElementById('questionDetailModal'));
            myModal.show();
        }
    </script>
    @endpush

</x-layouts.teacher>