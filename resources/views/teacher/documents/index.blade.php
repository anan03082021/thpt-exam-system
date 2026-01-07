<x-layouts.teacher title="Quản lý tài liệu">
    @push('styles')
    <style>
        .file-icon { font-size: 1.5rem; }
        .icon-pdf { color: #dc3545; }
        .icon-word { color: #0d6efd; }
        .icon-excel { color: #198754; }
        .icon-zip { color: #ffc107; }
    </style>
    @endpush

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-folder2-open me-2 text-warning"></i> Kho tài liệu</h5>
            <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-cloud-upload me-1"></i> Upload tài liệu
            </button>
        </div>

        {{-- Bộ lọc --}}
        <div class="card-body border-bottom bg-light">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-auto fw-bold text-muted">Lọc theo:</div>
                <div class="col-md-3">
                    <select name="topic_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Tất cả chủ đề --</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                                {{ $topic->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        {{-- Danh sách --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4">Tên tài liệu</th>
                        <th>Chủ đề</th>
                        <th>Kích thước</th>
                        <th>Ngày đăng</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        @php
                            $icon = 'bi-file-earmark';
                            $color = 'text-secondary';
                            if(in_array($doc->file_type, ['pdf'])) { $icon = 'bi-file-earmark-pdf-fill'; $color = 'icon-pdf'; }
                            if(in_array($doc->file_type, ['doc','docx'])) { $icon = 'bi-file-earmark-word-fill'; $color = 'icon-word'; }
                            if(in_array($doc->file_type, ['xls','xlsx'])) { $icon = 'bi-file-earmark-excel-fill'; $color = 'icon-excel'; }
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi {{ $icon }} {{ $color }} file-icon me-3"></i>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $doc->title }}</div>
                                        <div class="small text-muted text-uppercase">{{ $doc->file_type }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $doc->topic->name }}</span></td>
                            <td class="small text-muted">{{ round($doc->file_size / 1024, 2) }} KB</td>
                            <td class="small text-muted">{{ $doc->created_at->format('d/m/Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('teacher.documents.download', $doc->id) }}" class="btn btn-sm btn-light border text-primary" title="Tải xuống">
                                    <i class="bi bi-download"></i>
                                </a>
                                <form action="{{ route('teacher.documents.destroy', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa tài liệu này?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light border text-danger" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Chưa có tài liệu nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3">{{ $documents->links() }}</div>
    </div>

    {{-- MODAL UPLOAD --}}
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Upload tài liệu mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('teacher.documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên tài liệu <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="VD: Đề cương ôn tập HK1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chủ đề <span class="text-danger">*</span></label>
                            <select name="topic_id" class="form-select" required>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn file <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                            <div class="form-text small">Hỗ trợ: PDF, Word, Excel, PowerPoint (Max 10MB)</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary fw-bold">Upload ngay</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.teacher>