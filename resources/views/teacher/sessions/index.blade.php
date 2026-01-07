<x-layouts.teacher title="Quản lý Ca thi">

    @push('styles')
    <style>
        .card-header-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
        }
        .badge-status { font-weight: 600; font-size: 0.75rem; padding: 6px 12px; border-radius: 30px; }
        .bg-status-upcoming { background: #e0f2fe; color: #0284c7; } /* Xanh dương nhạt */
        .bg-status-ongoing { background: #dcfce7; color: #16a34a; }  /* Xanh lá */
        .bg-status-finished { background: #f3f4f6; color: #6b7280; } /* Xám */
    </style>
    @endpush

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Danh sách Ca thi</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        {{-- Header --}}
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-calendar-event me-2 text-primary"></i> Quản lý Ca thi / Kỳ thi
            </h5>
            <a href="{{ route('teacher.sessions.create') }}" class="btn btn-primary btn-sm fw-bold px-3 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tổ chức thi mới
            </a>
        </div>

        {{-- Table --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th>Tên Ca thi</th>
                            <th>Đề gốc</th>
                            <th>Thời gian</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Mật khẩu</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            @php
                                $now = now();
                                if ($now < $session->start_at) {
                                    $statusClass = 'bg-status-upcoming';
                                    $statusText = 'Sắp diễn ra';
                                    $icon = 'bi-hourglass';
                                } elseif ($now >= $session->start_at && $now <= $session->end_at) {
                                    $statusClass = 'bg-status-ongoing';
                                    $statusText = 'Đang diễn ra';
                                    $icon = 'bi-broadcast';
                                } else {
                                    $statusClass = 'bg-status-finished';
                                    $statusText = 'Đã kết thúc';
                                    $icon = 'bi-check-all';
                                }
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#{{ $session->id }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $session->title }}</div>
                                    <div class="small text-muted" title="Mã đề thi">
                                        Mã tham gia: <span class="font-monospace text-primary bg-light px-1 rounded">{{ $session->code ?? $session->id }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($session->exam)
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-file-earmark-text me-1"></i> {{ Str::limit($session->exam->title, 20) }}
                                        </span>
                                    @else
                                        <span class="text-danger small">Đề đã xóa</span>
                                    @endif
                                </td>
                                <td class="small">
                                    <div class="text-success"><i class="bi bi-play-circle me-1"></i> {{ \Carbon\Carbon::parse($session->start_at)->format('H:i d/m/Y') }}</div>
                                    <div class="text-danger"><i class="bi bi-stop-circle me-1"></i> {{ \Carbon\Carbon::parse($session->end_at)->format('H:i d/m/Y') }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge-status {{ $statusClass }}">
                                        <i class="bi {{ $icon }} me-1"></i> {{ $statusText }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($session->password)
                                        <span class="badge bg-warning text-dark" title="{{ $session->password }}">
                                            <i class="bi bi-key-fill"></i> Có
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
    <div class="dropdown">
        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Thao tác
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0">


            {{-- 2. Giám sát / Xem thí sinh (Kết nối vào trang SHOW) --}}
            <li>
                <a class="dropdown-item fw-bold text-primary" href="{{ route('teacher.sessions.show', $session->id) }}">
                    <i class="bi bi-speedometer2 me-2"></i> Giám sát & Thống kê
                </a>
            </li>

            {{-- 3. Xuất Excel --}}
            <li>
                <a class="dropdown-item" href="{{ route('teacher.sessions.export', $session->id) }}">
                    <i class="bi bi-file-earmark-excel me-2 text-success"></i> Xuất kết quả (Excel)
                </a>
            </li>

            {{-- 4. Chỉnh sửa --}}
            <li>
                <a class="dropdown-item" href="{{ route('teacher.sessions.edit', $session->id) }}">
                    <i class="bi bi-pencil-square me-2 text-info"></i> Sửa thông tin
                </a>
            </li>

            <li><hr class="dropdown-divider"></li>

            {{-- 5. Xóa Ca thi --}}
            <li>
                <form action="{{ route('teacher.sessions.destroy', $session->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy ca thi này? Dữ liệu bài làm của học sinh trong ca này cũng sẽ bị xóa!');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="bi bi-trash me-2"></i> Hủy ca thi
                    </button>
                </form>
            </li>
        </ul>
    </div>
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="opacity-25 mb-3">
                                        <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                                    </div>
                                    <h6 class="fw-bold text-secondary">Chưa có ca thi nào</h6>
                                    <p class="text-muted small mb-3">Hãy tạo ca thi để học sinh có thể bắt đầu làm bài.</p>
                                    <a href="{{ route('teacher.sessions.create') }}" class="btn btn-primary btn-sm">Tạo ngay</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($sessions->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Script Copy Link --}}
    @push('scripts')
    <script>
        function copyLink(url) {
            navigator.clipboard.writeText(url).then(function() {
                alert('Đã copy đường dẫn vào thi: ' + url);
            }, function(err) {
                console.error('Lỗi copy: ', err);
            });
        }
    </script>
    @endpush

</x-layouts.teacher>