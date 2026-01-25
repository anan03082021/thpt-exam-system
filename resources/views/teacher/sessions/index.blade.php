<x-layouts.teacher title="Quản lý Ca thi">

    @push('styles')
    <style>
        /* --- STYLE HIỆN ĐẠI (CLEAN UI) --- */
        :root {
            --primary-color: #4f46e5;
            --primary-bg: #eef2ff;
            --text-secondary: #64748b;
        }

        /* Card Styles */
        .card-custom {
            border: none;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.03), 0 4px 12px rgba(0,0,0,0.05);
            border-radius: 16px;
        }

        /* Table Styles */
        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-modern thead th {
            background-color: #f8fafc;
            color: var(--text-secondary);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .table-modern tbody tr { transition: background-color 0.2s; }
        .table-modern tbody tr:hover { background-color: #fcfcfc; }
        .table-modern td {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
            font-size: 0.95rem;
        }
        .table-modern tbody tr:last-child td { border-bottom: none; }

        /* Icon & Text Styling */
        .session-icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            background: #f0fdf4; color: #16a34a; 
            font-size: 1.25rem;
        }
        .session-title {
            font-weight: 700; color: #1e293b; text-decoration: none;
            font-size: 1rem; display: block;
            margin-bottom: 4px; transition: color 0.2s;
        }
        .session-title:hover { color: var(--primary-color); }
        .meta-text { font-size: 0.85rem; color: #94a3b8; display: flex; align-items: center; gap: 10px; }

        /* Status Badges */
        .badge-soft { padding: 0.5em 1em; font-size: 0.75rem; font-weight: 600; border-radius: 8px; }
        .badge-soft-upcoming { background-color: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe; } 
        .badge-soft-ongoing { background-color: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }  
        .badge-soft-finished { background-color: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; } 

        /* Buttons */
        .btn-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border: none; color: white;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
            transition: all 0.2s;
        }
        .btn-gradient:hover { transform: translateY(-2px); color: white; box-shadow: 0 6px 12px -1px rgba(79, 70, 229, 0.3); }
        
        .btn-icon {
            width: 36px; height: 36px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid #e2e8f0; color: #64748b; background: white;
            transition: all 0.2s;
        }
        .btn-icon:hover { border-color: var(--primary-color); color: var(--primary-color); background: #f8fafc; }
    </style>
    @endpush

    <div class="card card-custom bg-white mt-3" style="overflow: visible;">
        
        {{-- Header --}}
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-indigo-50 text-primary rounded-3 p-3 me-3 d-none d-md-block">
                    <i class="bi bi-calendar-event fs-4"></i>
                </div>
                <div>
                    <h4 class="mb-1 fw-bold text-dark">Quản lý Ca thi</h4>
                    <p class="mb-0 text-muted small">Tổ chức và giám sát các kỳ thi trực tuyến</p>
                </div>
            </div>
            
            <a href="{{ route('teacher.sessions.create') }}" class="btn btn-gradient fw-bold px-4 py-2 rounded-3 d-flex align-items-center">
                <i class="bi bi-plus-lg me-2"></i> 
                <span>Tổ chức thi mới</span>
            </a>
        </div>

        {{-- Toolbar --}}
        <div class="px-4 py-2 bg-light bg-opacity-25 border-bottom d-flex justify-content-between align-items-center">
            <div class="text-secondary fw-bold small text-uppercase ls-1">
                <i class="bi bi-layers me-2"></i> Tổng số: {{ $sessions->count() }} ca thi
            </div>
        </div>

        {{-- Table Content --}}
        <div>
            <table class="table-modern">
                <thead>
                    <tr>
                        <th width="30%">Thông tin Ca thi</th>
                        <th width="20%">Đề gốc</th>
                        <th width="20%">Thời gian</th>
                        <th width="15%" class="text-center">Trạng thái</th>
                        <th width="10%" class="text-center">Mật khẩu</th>
                        <th width="5%" class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        @php
                            $now = now();
                            if ($now < $session->start_at) {
                                $statusClass = 'badge-soft-upcoming';
                                $statusText = 'Sắp diễn ra';
                                $icon = 'bi-hourglass';
                            } elseif ($now >= $session->start_at && $now <= $session->end_at) {
                                $statusClass = 'badge-soft-ongoing';
                                $statusText = 'Đang diễn ra';
                                $icon = 'bi-broadcast';
                            } else {
                                $statusClass = 'badge-soft-finished';
                                $statusText = 'Đã kết thúc';
                                $icon = 'bi-check-all';
                            }
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-start">
                                    <div class="session-icon me-3 shadow-sm">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    <div>
                                        <div class="session-title text-truncate" style="max-width: 250px;" title="{{ $session->title }}">
                                            {{ $session->title }}
                                        </div>
                                        <div class="meta-text mt-1">
                                            <span><i class="bi bi-hash"></i> {{ $session->id }}</span>
                                            <span class="font-monospace bg-light border px-1 rounded text-dark" title="Mã tham gia">
                                                CODE: {{ $session->code ?? $session->id }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if($session->exam)
                                    <div class="d-flex align-items-center text-dark fw-bold">
                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                        <span class="text-truncate" style="max-width: 200px;" title="{{ $session->exam->title }}">
                                            {{ $session->exam->title }}
                                        </span>
                                    </div>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                        <i class="bi bi-exclamation-circle me-1"></i> Đề đã xóa
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex flex-column gap-1 small">
                                    <div class="text-success fw-bold">
                                        <i class="bi bi-play-circle me-2"></i> {{ \Carbon\Carbon::parse($session->start_at)->format('H:i d/m/Y') }}
                                    </div>
                                    <div class="text-danger fw-bold">
                                        <i class="bi bi-stop-circle me-2"></i> {{ \Carbon\Carbon::parse($session->end_at)->format('H:i d/m/Y') }}
                                    </div>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge badge-soft {{ $statusClass }}">
                                    <i class="bi {{ $icon }} me-1"></i> {{ $statusText }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if($session->password)
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-50" title="{{ $session->password }}">
                                        <i class="bi bi-key-fill me-1"></i> Có
                                    </span>
                                @else
                                    <span class="text-muted opacity-50"><i class="bi bi-dash-lg"></i></span>
                                @endif
                            </td>

                            <td class="text-end pe-4" style="position: relative;">
                                <div class="btn-group dropstart">
                                    <button class="btn btn-icon shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 p-1" style="min-width: 220px; z-index: 9999;">
                                        <li>
                                            <a class="dropdown-item py-2 rounded-2 fw-bold text-primary d-flex align-items-center" href="{{ route('teacher.sessions.show', $session->id) }}">
                                                <span class="bg-primary bg-opacity-10 text-primary rounded p-1 me-2"><i class="bi bi-speedometer2"></i></span>
                                                Giám sát & Thống kê
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 rounded-2 d-flex align-items-center" href="{{ route('teacher.sessions.export', $session->id) }}">
                                                <span class="bg-success bg-opacity-10 text-success rounded p-1 me-2"><i class="bi bi-file-earmark-excel"></i></span>
                                                Xuất Excel
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 rounded-2 d-flex align-items-center" href="{{ route('teacher.sessions.edit', $session->id) }}">
                                                <span class="bg-info bg-opacity-10 text-info rounded p-1 me-2"><i class="bi bi-pencil-square"></i></span>
                                                Sửa thông tin
                                            </a>
                                        </li>
                                        
                                        {{-- 
                                            [ĐÃ SỬA LỖI] 
                                            Sử dụng toán tử ?? để nếu code null thì dùng id 
                                        --}}
                                        <li>
                                            <button class="dropdown-item py-2 rounded-2 d-flex align-items-center" 
                                                    onclick="copyLink('{{ route('student.exam.join', ['code' => $session->code ?? $session->id]) }}')">
                                                <span class="bg-secondary bg-opacity-10 text-secondary rounded p-1 me-2"><i class="bi bi-link-45deg"></i></span>
                                                Copy Link thi
                                            </button>
                                        </li>

                                        <li><hr class="dropdown-divider my-1"></li>
                                        
                                        <li>
                                            <form action="{{ route('teacher.sessions.destroy', $session->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy ca thi này?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item py-2 rounded-2 text-danger d-flex align-items-center">
                                                    <span class="bg-danger bg-opacity-10 text-danger rounded p-1 me-2"><i class="bi bi-trash"></i></span>
                                                    Hủy ca thi
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
                                <div class="py-5">
                                    <div class="mb-3">
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="bi bi-calendar-x text-muted opacity-50" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark">Chưa có ca thi nào</h6>
                                    <p class="text-muted small mb-4">Hãy tạo ca thi để học sinh có thể bắt đầu làm bài.</p>
                                    <a href="{{ route('teacher.sessions.create') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-bold">
                                        Tạo ngay
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sessions->hasPages())
            <div class="card-footer bg-white border-top py-4 d-flex justify-content-center">
                {{ $sessions->links('pagination::bootstrap-5') }}
            </div>
        @endif

        <div style="height: 50px;"></div>
    </div>

    @push('scripts')
    <script>
        function copyLink(url) {
            navigator.clipboard.writeText(url).then(function() {
                alert('Đã copy đường dẫn vào thi: ' + url);
            }, function(err) {
                console.error('Lỗi copy: ', err);
                alert('Không thể copy link. Vui lòng thử lại thủ công.');
            });
        }
    </script>
    @endpush

</x-layouts.teacher>