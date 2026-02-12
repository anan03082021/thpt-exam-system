<x-layouts.admin title="Kiểm duyệt Diễn đàn">

    <div class="container-fluid px-4 mt-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Kiểm duyệt Diễn đàn</h2>
                <p class="text-muted mb-0">Quản lý và xóa các tin nhắn vi phạm tiêu chuẩn cộng đồng.</p>
            </div>
        </div>

        {{-- Main Card --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 px-4">
                <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-shield-exclamation me-2"></i> Danh sách tin nhắn gần đây</h6>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Người gửi</th>
                            <th style="width: 50%;">Nội dung</th>
                            <th>Loại tin</th>
                            <th>Thời gian</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $msg)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        {{-- Avatar giả lập --}}
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2 text-primary fw-bold" 
                                             style="width: 35px; height: 35px;">
                                            {{ substr($msg->user->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $msg->user->name ?? 'Người dùng đã xóa' }}</div>
                                            <div class="small text-muted" style="font-size: 0.8rem;">ID: {{ $msg->user_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="p-2 bg-light rounded text-break border">
                                        {{ $msg->message }}
                                    </div>
                                </td>
                                <td>
                                    @if($msg->type == 'announcement')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                            <i class="bi bi-megaphone-fill me-1"></i> Thông báo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                            <i class="bi bi-chat-dots me-1"></i> Thảo luận
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $msg->created_at->format('H:i d/m/Y') }}
                                </td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('admin.forum.destroy', $msg->id) }}" method="POST" 
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa tin nhắn này không? Hành động này không thể hoàn tác.');">
                                        @csrf 
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Xóa tin nhắn">
                                            <i class="bi bi-trash"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-chat-square-text display-6 d-block mb-3 opacity-50"></i>
                                    Chưa có tin nhắn nào trong hệ thống.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-center">
                {{ $messages->links() }}
            </div>
        </div>
    </div>

</x-layouts.admin>