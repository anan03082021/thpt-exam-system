<x-layouts.shared title="Kết quả: {{ $exam->title }}">

    @push('styles')
    <style>
        .hover-scale { transition: transform 0.2s; }
        .hover-scale:hover { transform: scale(1.1); background-color: var(--primary-bg); color: var(--primary-color); border-color: var(--primary-color) !important; }
        :root { --primary-color: #4f46e5; --primary-bg: #eef2ff; --text-secondary: #64748b; }

        /* Stat Cards */
        .stat-card { background: white; border: none; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem; }
        
        .stat-primary .stat-icon { background: #eef2ff; color: #4f46e5; }
        .stat-success .stat-icon { background: #ecfdf5; color: #059669; }
        .stat-warning .stat-icon { background: #fffbeb; color: #d97706; }
        .stat-danger .stat-icon  { background: #fef2f2; color: #dc2626; }

        .card-custom { border: none; border-radius: 16px; box-shadow: 0 0 0 1px rgba(0,0,0,0.03); overflow: hidden; }
        .table-modern { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-modern th { background: #f8fafc; color: var(--text-secondary); font-size: 0.75rem; text-transform: uppercase; padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0; }
        .table-modern td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
        
        .user-avatar { width: 40px; height: 40px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #64748b; }
        .badge-score { font-size: 0.9rem; font-weight: 700; padding: 0.3em 0.8em; border-radius: 8px; }
        .badge-score-high { background: #ecfdf5; color: #059669; }
        .badge-score-mid { background: #eff6ff; color: #2563eb; }
        .badge-score-low { background: #fef2f2; color: #dc2626; }
    </style>
    @endpush

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            <h4 class="fw-bold text-dark mb-0" style="font-size: 1.75rem;">{{ $exam->title }}</h4>
            <p class="text-muted small mb-0 mt-2">
                <i class="bi bi-clock me-1"></i> Ngày tạo: {{ $exam->created_at->format('d/m/Y') }} &bull; 
                <i class="bi bi-people me-1 ms-2"></i> {{ $attempts->count() }} bài nộp
            </p>
        </div>
    </div>

    {{-- DASHBOARD --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="stat-card stat-primary"><div class="stat-icon"><i class="bi bi-files"></i></div><h2 class="fw-bold text-dark mb-0">{{ $attempts->count() }}</h2><span class="text-secondary small fw-bold text-uppercase">Tổng số bài</span></div></div>
        <div class="col-md-3"><div class="stat-card stat-warning"><div class="stat-icon"><i class="bi bi-pie-chart"></i></div><h2 class="fw-bold text-dark mb-0">{{ number_format($attempts->avg('total_score'), 2) }}</h2><span class="text-secondary small fw-bold text-uppercase">Điểm trung bình</span></div></div>
        <div class="col-md-3"><div class="stat-card stat-success"><div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div><h2 class="fw-bold text-dark mb-0">{{ $attempts->max('total_score') ?? 0 }}</h2><span class="text-secondary small fw-bold text-uppercase">Cao nhất</span></div></div>
        <div class="col-md-3"><div class="stat-card stat-danger"><div class="stat-icon"><i class="bi bi-graph-down-arrow"></i></div><h2 class="fw-bold text-dark mb-0">{{ $attempts->min('total_score') ?? 0 }}</h2><span class="text-secondary small fw-bold text-uppercase">Thấp nhất</span></div></div>
    </div>

    {{-- DANH SÁCH --}}
    <div class="card card-custom bg-white">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-dark mb-0">Bảng điểm chi tiết</h5>
            <div class="input-group" style="width: 280px;">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0 shadow-none" placeholder="Tìm kiếm học sinh...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th width="35%">Học sinh</th>
                        <th width="20%">Thời gian nộp</th>
                        <th width="15%" class="text-center">Điểm số</th>
                        <th width="15%" class="text-center">Xếp loại</th>
                        <th width="10%" class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
    @forelse($attempts as $index => $attempt)
        <tr>
            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-3">{{ substr($attempt->user->name ?? 'U', 0, 1) }}</div>
                    <div>
                        <div class="fw-bold text-dark">{{ $attempt->user->name ?? 'Ẩn danh' }}</div>
                        <div class="small text-muted">{{ $attempt->user->email ?? '' }}</div>
                    </div>
                </div>
            </td>
            <td class="text-secondary"><i class="bi bi-calendar3 me-1"></i> {{ $attempt->created_at->format('H:i d/m/Y') }}</td>
            <td class="text-center">
                @php 
                    $s = $attempt->total_score; 
                @endphp
                <span class="badge-score {{ $s >= 8 ? 'badge-score-high' : ($s >= 5 ? 'badge-score-mid' : 'badge-score-low') }}">{{ $s }}</span>
            </td>
            <td class="text-center">
                @if($s >= 8) <span class="text-success fw-bold small">Giỏi</span>
                @elseif($s >= 6.5) <span class="text-primary fw-bold small">Khá</span>
                @elseif($s >= 5) <span class="text-warning fw-bold small">Trung bình</span>
                @else <span class="text-danger fw-bold small">Yếu</span>
                @endif
            </td>
            <td class="text-end pe-4">
                {{-- TÍNH TOÁN DỮ LIỆU THỰC TẾ --}}
                @php
                    // 1. Lấy tổng số câu của đề (Nếu đề chưa set thì mặc định là 1 để tránh chia cho 0)
                    $totalQ = $exam->total_questions > 0 ? $exam->total_questions : 1;
                    
                    // 2. Tính số câu đúng thực tế
                    // Công thức: (Điểm đạt được / 10) * Tổng số câu
                    // Ví dụ: 8 điểm / 10 * 40 câu = 32 câu đúng
                    $correctCount = round(($attempt->total_score / 10) * $totalQ);
                    
                    // Lưu ý: Nếu database của bạn có lưu cột 'correct_answers_count' trong bảng attempts
                    // thì thay dòng trên bằng: $correctCount = $attempt->correct_answers_count;
                @endphp

                <button type="button" 
                        class="btn btn-sm btn-light border rounded-circle shadow-sm text-primary hover-scale" 
                        title="Xem thống kê"
                        {{-- Truyền dữ liệu thực vào đây --}}
                        data-name="{{ $attempt->user->name ?? 'Ẩn danh' }}"
                        data-score="{{ $attempt->total_score }}"
                        data-time="{{ $attempt->created_at->format('H:i d/m/Y') }}"
                        data-id="{{ $attempt->id }}"
                        data-correct="{{ $correctCount }}"
                        data-total="{{ $totalQ }}"
                        onclick="openResultModal(this)">
                    <i class="bi bi-eye-fill"></i>
                </button>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center py-5 text-muted">Chưa có bài nộp nào.</td></tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>

{{-- MODAL THỐNG KÊ (Đã sửa lỗi hiển thị) --}}
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            {{-- Header: Thêm pb-5 để tạo khoảng trống bên dưới cho vòng tròn lấn sang --}}
            <div class="modal-header bg-primary text-white border-0 px-3 pt-3 pb-5 position-relative">
                <div class="text-center w-100">
                    <h6 class="modal-title fw-bold text-truncate mx-auto mb-1" id="modalName" style="max-width: 200px;">Học sinh</h6>
                    <p class="mb-0 small opacity-75"><i class="bi bi-clock me-1"></i> <span id="modalTime">--:--</span></p>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body px-4 pb-4 pt-0 text-center bg-white position-relative">
                
                {{-- Vòng tròn điểm số: Dùng translate-middle để căn giữa tuyệt đối ngay mép --}}
                <div class="position-absolute top-0 start-50 translate-middle">
                    <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                         style="width: 100px; height: 100px; border: 5px solid #fff;">
                        <div>
                            <span class="display-6 fw-bolder text-primary" id="modalScore" style="letter-spacing: -1px;">0.0</span>
                            {{-- <span class="d-block text-muted fw-bold" style="font-size: 0.55rem; margin-top: -5px;">ĐIỂM</span> --}}
                        </div>
                    </div>
                </div>

                {{-- Khoảng trống đệm để nội dung không bị vòng tròn che (Margin Top) --}}
                <div style="margin-top: 60px;">
                    
                    {{-- Badge Xếp loại --}}
                    <div class="mb-4">
                        <span class="badge bg-light text-dark border px-3 py-1 rounded-pill fw-bold text-uppercase shadow-sm" id="modalRankBadge">
                            --
                        </span>
                    </div>

                    {{-- Bảng thống kê --}}
                    <div class="bg-light rounded-3 p-3 border border-dashed">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-muted small fw-bold">Tổng số câu</span>
                            <span class="fw-bold text-dark" id="modalTotalQ">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Làm đúng</span>
                            <div>
                                <span class="fw-bold text-success" id="modalCorrect">0</span>
                                <span class="small text-muted" id="modalCorrectPercent" style="font-size: 0.75rem">(0%)</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-danger small fw-bold"><i class="bi bi-x-circle-fill me-1"></i> Làm sai</span>
                            <div>
                                <span class="fw-bold text-danger" id="modalWrong">0</span>
                                <span class="small text-muted" id="modalWrongPercent" style="font-size: 0.75rem">(0%)</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 text-muted" style="font-size: 0.65rem;">
                        Hệ thống thống kê tự động
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openResultModal(button) {
        // 1. Lấy dữ liệu THỰC TẾ từ nút bấm
        const name = button.getAttribute('data-name');
        const score = parseFloat(button.getAttribute('data-score'));
        const time = button.getAttribute('data-time');
        const id = button.getAttribute('data-id');
        
        // Lấy số câu đúng/tổng câu từ PHP truyền sang
        const correct = parseInt(button.getAttribute('data-correct')) || 0;
        const total = parseInt(button.getAttribute('data-total')) || 0;
        
        // Tính số câu sai
        const wrong = total - correct;

        // Tính phần trăm để hiển thị (tránh lỗi chia cho 0)
        let correctPct = 0;
        if(total > 0) {
            correctPct = Math.round((correct / total) * 100);
        }
        const wrongPct = 100 - correctPct;

        // 2. Logic Xếp loại (Giữ nguyên)
        let rank = 'Yếu', color = 'text-danger', border = 'border-danger';
        if (score >= 8) { rank = 'Giỏi'; color = 'text-success'; border = 'border-success'; }
        else if (score >= 6.5) { rank = 'Khá'; color = 'text-primary'; border = 'border-primary'; }
        else if (score >= 5) { rank = 'Trung bình'; color = 'text-warning'; border = 'border-warning'; }

        // 3. Gán dữ liệu vào Modal
        document.getElementById('modalName').innerText = name;
        document.getElementById('modalTime').innerText = time;
        document.getElementById('modalScore').innerText = score;
        
        // Cập nhật Badge Xếp loại
        const rankBadge = document.getElementById('modalRankBadge');
        rankBadge.innerText = rank;
        rankBadge.className = `badge bg-white border px-4 py-2 rounded-pill fw-bold text-uppercase shadow-sm ${color} ${border}`;

        // Cập nhật Bảng thống kê số liệu thực
        document.getElementById('modalTotalQ').innerText = total + " câu";
        document.getElementById('modalCorrect').innerText = correct;
        document.getElementById('modalCorrectPercent').innerText = `(${correctPct}%)`;
        document.getElementById('modalWrong').innerText = wrong;
        document.getElementById('modalWrongPercent').innerText = `(${wrongPct}%)`;

        // 4. Mở Modal
        const myModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('resultModal'));
        myModal.show();
    }
</script>
@endpush

</x-layouts.shared>