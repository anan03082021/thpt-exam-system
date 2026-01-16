<x-app-layout>
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* Nền xám rất nhạt */
        }

        /* --- 1. STYLE BANNER HIỆN ĐẠI --- */
        .dashboard-banner {
            /* Gradient đa sắc màu hiện đại */
            background: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 50%, #ec4899 100%) !important;
            color: white !important;
            border-radius: 20px;
            padding: 2rem 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.3);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .banner-content h2 { font-weight: 800; letter-spacing: -0.5px; }
        .banner-deco {
            position: absolute; opacity: 0.15; right: -30px; bottom: -50px;
            font-size: 8rem; color: white; transform: rotate(-15deg); filter: blur(2px);
        }

        /* --- 2. STYLE THẺ HIỆN ĐẠI (Dùng chung cho Chat & Kỳ thi) --- */
        .modern-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03); /* Đổ bóng rất nhẹ */
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
        }
        .modern-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.06);
            border-color: #e2e8f0;
        }

        /* --- 3. STYLE KHUNG CHAT TINH TẾ (COMPACT) --- */
        .forum-section { height: 300px; margin-bottom: 2.5rem; }
        
        .chat-card { display: flex; flex-direction: column; }

        .chat-header {
            padding: 10px 15px; font-size: 0.85rem; font-weight: 700;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid #f1f5f9;
        }
        /* Header màu nhẹ nhàng */
        .chat-header.announcement { background: #fef2f2; color: #991b1b; }
        .chat-header.general { background: #f0f9ff; color: #075985; }

        .messages-area {
            flex: 1; overflow-y: auto; padding: 12px 15px; background: #ffffff;
            display: flex; flex-direction: column; gap: 10px; font-size: 0.85rem;
        }
        .messages-area::-webkit-scrollbar { width: 5px; }
        .messages-area::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }

        .msg-item { max-width: 88%; display: flex; flex-direction: column; }
        .msg-left { align-self: flex-start; }
        .msg-right { align-self: flex-end; align-items: flex-end; }

        /* Bong bóng chat tròn trịa hiện đại */
        .msg-bubble {
            padding: 8px 14px; border-radius: 18px; line-height: 1.4; position: relative; word-wrap: break-word;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .msg-left .msg-bubble { background: #f1f5f9; color: #334155; border-bottom-left-radius: 4px; }
        /* Gradient cho tin nhắn của mình */
        .msg-right .msg-bubble { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; border-bottom-right-radius: 4px; }

        .msg-info { font-size: 0.7rem; color: #94a3b8; margin-bottom: 3px; font-weight: 500; }

        /* Thông báo */
        .announcement-bubble { 
            background: #fff1f2; border: 1px solid #fecdd3; color: #881337; 
            padding: 10px 12px; border-radius: 12px; font-size: 0.85rem;
        }

        /* Input area */
        .input-area { padding: 10px; background: white; border-top: 1px solid #f1f5f9; display: flex; gap: 8px; align-items: center; }
        .chat-input {
            flex: 1; border: 1px solid #e2e8f0; border-radius: 24px; padding: 8px 16px;
            font-size: 0.85rem; outline: none; transition: all 0.2s; background: #f8fafc;
        }
        .chat-input:focus { border-color: #6366f1; background: white; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
        .btn-send {
            width: 36px; height: 36px; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center;
            color: white; cursor: pointer; transition: transform 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn-send:hover { transform: scale(1.05); }
        .btn-send.general { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); }
        .btn-send.announcement { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); }

        /* --- 4. STYLE DANH SÁCH THI --- */
        .section-title { font-weight: 800; color: #1e293b; letter-spacing: -0.5px; }
        .exam-card .card-body { padding: 1.25rem; }
        .status-badge {
            font-size: 0.65rem; font-weight: 800; padding: 5px 10px;
            border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .status-ongoing { background: #dcfce7; color: #166534; }
        .status-upcoming { background: #fef9c3; color: #854d0e; }
        
        .exam-title { font-weight: 700; color: #334155; font-size: 1rem; }
        .exam-meta { font-size: 0.8rem; color: #64748b; display: flex; gap: 15px; align-items: center; }
        .exam-meta i { color: #6366f1; }

        .btn-exam {
            border-radius: 12px; font-weight: 700; padding: 8px 15px; font-size: 0.9rem;
            transition: all 0.2s; border: none;
        }
        .btn-take { background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); }
        .btn-take:hover { box-shadow: 0 6px 10px -1px rgba(16, 185, 129, 0.3); transform: translateY(-1px); }
        .btn-wait { background: #e2e8f0; color: #94a3b8; cursor: not-allowed; }
    </style>
    @endpush

    <div class="container mt-3">
        @if(session('error') || session('success'))
            <div class="alert {{ session('error') ? 'alert-danger' : 'alert-success' }} py-2 fs-6 shadow-sm border-0 rounded-3 mb-4">
                <i class="bi {{ session('error') ? 'bi-exclamation-triangle-fill text-danger' : 'bi-check-circle-fill text-success' }} me-2"></i> 
                {{ session('error') ?? session('success') }}
            </div>
        @endif
    </div>

    <div class="container pb-5">
        {{-- 1. BANNER HIỆN ĐẠI --}}
        <div class="dashboard-banner d-flex align-items-center justify-content-between">
            <div class="banner-content position-relative z-1">
                <h2 class="mb-2">Xin chào, {{ Auth::user()->name }}! 🎉</h2>
                <p class="mb-0 opacity-90" style="font-size: 1.05rem;">Chúc bạn một ngày học tập năng suất và hiệu quả.</p>
            </div>
            <i class="bi bi-mortarboard-fill banner-deco"></i>
        </div>

        {{-- 2. WIDGET DIỄN ĐÀN NHỎ GỌN & TINH TẾ --}}
        <div class="forum-section row g-3">
            {{-- THÔNG BÁO (35%) --}}
            <div class="col-lg-4 h-100">
                <div class="modern-card chat-card">
                    <div class="chat-header announcement">
                        <span><i class="bi bi-megaphone-fill me-2"></i> Bảng Thông Báo</span>
                        <i class="bi bi-bell-fill opacity-50"></i>
                    </div>
                    
                    <div class="messages-area" id="announcement-list">
                        {{-- JS load thông báo --}}
                    </div>

                    @if(Auth::user()->role !== 'student')
                        <div class="input-area">
                            <input type="text" id="announcement-input" class="chat-input" placeholder="Đăng thông báo..." onkeypress="handleEnter(event, 'announcement')">
                            <button class="btn-send announcement" onclick="sendMessage('announcement')"><i class="bi bi-send-fill fs-6"></i></button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- CHAT CHUNG (65%) --}}
            <div class="col-lg-8 h-100">
                <div class="modern-card chat-card">
                    <div class="chat-header general">
                        <span><i class="bi bi-chat-dots-fill me-2"></i> Thảo Luận Chung</span>
                        <span class="badge bg-white text-primary border fw-bold" style="font-size: 0.6rem;">Trực tuyến</span>
                    </div>
                    
                    <div class="messages-area" id="general-list">
                        {{-- JS load tin nhắn --}}
                    </div>

                    <div class="input-area">
                        <input type="text" id="general-input" class="chat-input" placeholder="Nhập tin nhắn thảo luận..." onkeypress="handleEnter(event, 'general')">
                        <button class="btn-send general" onclick="sendMessage('general')"><i class="bi bi-send-fill fs-6"></i></button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. DANH SÁCH KỲ THI --}}
        <div class="d-flex align-items-center mb-4 mt-5">
            <h4 class="section-title mb-0">
                <i class="bi bi-grid-fill text-indigo-600 me-2" style="color: #4f46e5;"></i> Danh sách kỳ thi
            </h4>
        </div>

        @if(isset($officialSessions) && $officialSessions->count() > 0)
            <div class="row g-3">
                @foreach($officialSessions as $session)
                    @php
                        $now = \Carbon\Carbon::now();
                        $isUpcoming = $now < $session->start_at;
                        $isOngoing = $now >= $session->start_at && $now <= $session->end_at;
                        
                        $statusBadge = $isOngoing ? 'status-ongoing' : 'status-upcoming';
                        $statusText = $isOngoing ? 'Đang diễn ra' : 'Sắp tới';
                        $borderClass = $isOngoing ? 'border-emerald-400' : 'border-amber-300';
                    @endphp

                    <div class="col-md-6 col-lg-4">
                        <div class="modern-card exam-card h-100 d-flex flex-column">
                            <div class="card-body d-flex flex-column flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="status-badge {{ $statusBadge }}">
                                        <i class="bi {{ $isOngoing ? 'bi-broadcast' : 'bi-clock' }} me-1"></i> {{ $statusText }}
                                    </span>
                                    @if($session->password)
                                        <i class="bi bi-shield-lock-fill text-muted" data-bs-toggle="tooltip" title="Yêu cầu mật khẩu"></i>
                                    @endif
                                </div>

                                <h6 class="exam-title text-truncate mb-3" title="{{ $session->title }}">
                                    {{ $session->title }}
                                </h6>
                                
                                <div class="exam-meta mb-4">
                                    <div><i class="bi bi-hourglass-split"></i> {{ $session->exam->duration }}'</div>
                                    <div><i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse($session->start_at)->format('H:i d/m') }}</div>
                                </div>

                                <div class="mt-auto">
                                    @if($isUpcoming)
                                        <button disabled class="btn-exam btn-wait w-100">
                                            <i class="bi bi-lock-fill me-1"></i> Chưa mở
                                        </button>
                                    @else
                                        <a href="{{ route('exam.take', $session->id) }}" class="btn-exam btn-take w-100 d-block text-center text-decoration-none">
                                            Vào thi ngay <i class="bi bi-arrow-right-short ms-1 fs-5" style="vertical-align: middle;"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border-0">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Empty" style="width: 80px; opacity: 0.5;">
                <h6 class="fw-bold text-secondary mt-3">Chưa có kỳ thi nào</h6>
                <p class="text-muted small mb-0">Danh sách kỳ thi sẽ xuất hiện tại đây.</p>
            </div>
        @endif
    </div>

    {{-- JS GIỮ NGUYÊN --}}
    @push('scripts')
    <script>
        const currentUserId = {{ Auth::id() }};
        const fetchUrl = "{{ route('forum.fetch') }}";
        const sendUrl = "{{ route('forum.send') }}";

        function loadMessages() {
            fetch(fetchUrl).then(res => res.json()).then(data => renderMessages(data)).catch(err => console.error(err));
        }

        // Render HTML (Đã cập nhật logic tô màu tên Giáo viên)
function renderMessages(messages) {
            let generalHtml = '';
            let announcementHtml = '';

            messages.forEach(msg => {
                let time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                // Kiểm tra xem người gửi có phải là Giáo viên hoặc Admin không
                let isTeacher = (msg.user.role === 'teacher' || msg.user.role === 'admin');

                // 1. KÊNH THÔNG BÁO
                if(msg.type === 'announcement') {
                    announcementHtml += `
                        <div class="announcement-item mb-2">
                            <div class="msg-info fw-bold" style="color: #dc2626;">
                                <i class="bi bi-patch-check-fill me-1"></i>${msg.user.name} • ${time}
                            </div>
                            <div class="announcement-bubble fw-bold">${msg.message}</div>
                        </div>`;
                } 
                // 2. KÊNH THẢO LUẬN CHUNG
                else {
                    let isMe = msg.user_id === currentUserId;
                    let align = isMe ? 'msg-right' : 'msg-left';
                    
                    // Xử lý tên hiển thị
                    let displayName = isMe ? 'Bạn' : msg.user.name;
                    
                    if (!isMe && isTeacher) {
                        // Tên Giáo viên: Màu cam + Icon
                        displayName = `<span style="color: #ea580c; font-weight: 800;">
                            <i class="bi bi-patch-check-fill me-1"></i>${msg.user.name} 
                            <span style="font-size:0.6rem; border:1px solid #ea580c; padding:0 3px; border-radius:4px;">GV</span>
                        </span>`;
                    } else if (!isMe) {
                        // Tên Học sinh: Màu xám
                        displayName = `<span style="color: #64748b; font-weight: 600;">${msg.user.name}</span>`;
                    }

                    // [MỚI] Xử lý Style bong bóng chat
                    // Nếu là GV -> In đậm + Thêm viền cam nhẹ để nổi bật
                    let bubbleStyle = isTeacher 
                        ? 'font-weight: 700; border: 2px solid #fdba74; box-shadow: 0 2px 4px rgba(234, 88, 12, 0.1);' 
                        : '';

                    generalHtml += `
                        <div class="msg-item ${align} mb-2">
                            <div class="msg-info" style="font-size:0.7rem">
                                ${displayName} <span class="opacity-50 ms-1 text-muted fw-normal">• ${time}</span>
                            </div>
                            <div class="msg-bubble" style="${bubbleStyle}">${msg.message}</div>
                        </div>`;
                }
            });

            const genBox = document.getElementById('general-list');
            const annBox = document.getElementById('announcement-list');

            if(genBox.innerHTML !== generalHtml) {
                genBox.innerHTML = generalHtml;
                genBox.scrollTop = genBox.scrollHeight;
            }
            if(annBox.innerHTML !== announcementHtml) {
                annBox.innerHTML = announcementHtml;
                annBox.scrollTop = annBox.scrollHeight;
            }
        }

        function sendMessage(type) {
            let inputId = type === 'general' ? 'general-input' : 'announcement-input';
            let inputEl = document.getElementById(inputId);
            let message = inputEl.value.trim();
            if(!message) return;
            fetch(sendUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({ message, type })
            }).then(res => res.json()).then(() => { inputEl.value = ''; loadMessages(); });
        }
        function handleEnter(e, type) { if(e.key === 'Enter') sendMessage(type); }

        document.addEventListener('DOMContentLoaded', () => {
            loadMessages();
            setInterval(loadMessages, 3000);
            // Kích hoạt tooltip của Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
    @endpush
</x-app-layout>