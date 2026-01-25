<x-layouts.teacher title="Bảng điều khiển Giáo viên">

    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f3f4f6; }

        /* WELCOME BANNER */
        .welcome-card {
            background: linear-gradient(120deg, #4f46e5 0%, #818cf8 100%);
            border-radius: 16px; padding: 2rem; color: white;
            position: relative; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2);
        }
        .welcome-card h2 { font-weight: 700; font-size: 1.75rem; margin-bottom: 0.5rem; }
        .welcome-deco {
            position: absolute; right: 20px; bottom: -20px; font-size: 8rem; opacity: 0.15; color: white;
            transform: rotate(10deg);
        }

        /* STATS CARDS */
        .stat-card {
            background: white; border-radius: 12px; padding: 1.5rem;
            border: 1px solid #e5e7eb; transition: all 0.2s;
            display: flex; align-items: center; gap: 1rem; height: 100%;
        }
        .stat-card:hover { transform: translateY(-3px); border-color: #4f46e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .stat-icon-wrapper {
            width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem;
        }
        .bg-indigo-soft { background: #eef2ff; color: #4f46e5; }
        .bg-emerald-soft { background: #ecfdf5; color: #10b981; }
        .bg-amber-soft { background: #fffbeb; color: #d97706; }

        /* SECTION TITLES */
        .section-title { font-size: 1rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        
        /* CHAT WIDGET */
        .chat-widget { background: white; border-radius: 16px; border: 1px solid #e5e7eb; height: 500px; display: flex; flex-direction: column; overflow: hidden; }
        .chat-tabs .nav-link {
            border: none; border-bottom: 2px solid transparent; color: #6b7280; font-weight: 600; padding: 1rem;
        }
        .chat-tabs .nav-link.active { color: #4f46e5; border-bottom-color: #4f46e5; background: none; }
        .chat-body { flex: 1; overflow-y: auto; padding: 1rem; background: #f9fafb; display: flex; flex-direction: column; gap: 0.75rem; }
        .chat-input-area { padding: 0.75rem; background: white; border-top: 1px solid #e5e7eb; display: flex; gap: 0.5rem; }
        
        .msg-bubble { padding: 0.6rem 1rem; border-radius: 12px; font-size: 0.9rem; max-width: 85%; position: relative; }
        .msg-left { align-self: flex-start; background: white; border: 1px solid #e5e7eb; color: #374151; border-bottom-left-radius: 2px; }
        .msg-right { align-self: flex-end; background: #4f46e5; color: white; border-bottom-right-radius: 2px; }
        .msg-meta { font-size: 0.7rem; margin-bottom: 2px; opacity: 0.8; }

        /* Announcement Style inside Chat */
        .announcement-bubble { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; border-left: 4px solid #be123c; }
    </style>
    @endpush

    <div class="container py-4">
        
        {{-- 1. WELCOME SECTION --}}
        <div class="welcome-card mb-5">
            <div class="row align-items-center">
                <div class="col-md-9">
                    <h2>Xin chào, Thầy/Cô {{ Auth::user()->name }}! 👋</h2>
                    <p class="mb-0 opacity-90">Chúc thầy/cô một ngày giảng dạy hiệu quả.</p>
                </div>
            </div>
            <i class="bi bi-book welcome-deco"></i>
        </div>

        <div class="row g-4">
            
            {{-- CỘT TRÁI: THỐNG KÊ (ĐÃ BỎ QUICK ACTIONS) --}}
            <div class="col-lg-8">
                <h5 class="section-title mb-3"><i class="bi bi-bar-chart-line-fill text-indigo"></i> Tổng quan số liệu</h5>
                
                {{-- STATS GRID --}}
                <div class="row g-4 mb-4">
                    {{-- Card 1 --}}
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon-wrapper bg-indigo-soft"><i class="bi bi-collection"></i></div>
                            <div>
                                <div class="text-muted small fw-bold text-uppercase ls-1">Ngân hàng đề</div>
                                <div class="fs-2 fw-bold text-dark">{{ $totalExams ?? 0 }}</div>
                                <div class="small text-muted">Đề thi đã soạn</div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Card 2 --}}
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon-wrapper bg-emerald-soft"><i class="bi bi-broadcast"></i></div>
                            <div>
                                <div class="text-muted small fw-bold text-uppercase ls-1">Tổ chức thi</div>
                                <div class="fs-2 fw-bold text-dark">{{ $totalSessions ?? 0 }}</div>
                                <div class="small text-muted">Ca thi đã tạo</div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3 --}}
                    <div class="col-md-12">
                        <div class="stat-card">
                            <div class="stat-icon-wrapper bg-amber-soft"><i class="bi bi-people-fill"></i></div>
                            <div>
                                <div class="text-muted small fw-bold text-uppercase ls-1">Học sinh</div>
                                <div class="fs-2 fw-bold text-dark">{{ $totalStudents ?? 0 }}</div>
                                <div class="small text-muted">Tổng số tài khoản học sinh trong hệ thống</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: THẢO LUẬN & THÔNG BÁO --}}
            <div class="col-lg-4">
                <h5 class="section-title mb-3"><i class="bi bi-chat-dots-fill text-primary"></i> Trao đổi & Thông báo</h5>
                
                <div class="chat-widget">
                    {{-- Tabs --}}
                    <ul class="nav nav-tabs chat-tabs nav-fill" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general" onclick="setCurrentTab('general')">Thảo luận lớp</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-danger" data-bs-toggle="tab" data-bs-target="#tab-announce" onclick="setCurrentTab('announcement')">
                                <i class="bi bi-megaphone-fill me-1"></i> Thông báo
                            </button>
                        </li>
                    </ul>

                    {{-- Chat Content --}}
                    <div class="tab-content flex-grow-1 d-flex flex-column" style="overflow: hidden;">
                        
                        {{-- Tab Thảo luận --}}
                        <div class="tab-pane fade show active h-100" id="tab-general">
                            <div class="chat-body" id="general-list">
                                {{-- JS load tin nhắn --}}
                            </div>
                        </div>

                        {{-- Tab Thông báo --}}
                        <div class="tab-pane fade h-100" id="tab-announce">
                            <div class="chat-body bg-white" id="announcement-list">
                                {{-- JS load thông báo --}}
                            </div>
                        </div>
                    </div>

                    {{-- Input Area --}}
                    <div class="chat-input-area">
                        <input type="text" id="chat-input" class="form-control rounded-pill bg-light border-0" placeholder="Nhập tin nhắn..." onkeypress="handleEnter(event)">
                        <button class="btn btn-primary rounded-circle" style="width: 40px; height: 40px;" onclick="sendMessage()">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        const currentUserId = {{ Auth::id() }};
        const fetchUrl = "{{ route('forum.fetch') }}";
        const sendUrl = "{{ route('forum.send') }}";
        let currentTab = 'general'; 

        function setCurrentTab(tab) {
            currentTab = tab;
            const input = document.getElementById('chat-input');
            if(tab === 'announcement') {
                input.placeholder = "Đăng thông báo quan trọng...";
            } else {
                input.placeholder = "Nhập tin nhắn thảo luận...";
            }
        }

        function loadMessages() {
            fetch(fetchUrl).then(res => res.json()).then(data => renderMessages(data)).catch(err => console.error(err));
        }

        function renderMessages(messages) {
            let genHtml = '';
            let annHtml = '';

            messages.forEach(msg => {
                let time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                // Render Announcement
                if(msg.type === 'announcement') {
                    annHtml += `
                        <div class="msg-bubble announcement-bubble w-100 mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <strong class="small">${msg.user.name}</strong>
                                <span class="small opacity-75">${time}</span>
                            </div>
                            <div>${msg.message}</div>
                        </div>`;
                } 
                // Render General Chat
                else {
                    let isMe = msg.user_id === currentUserId;
                    let alignClass = isMe ? 'msg-right' : 'msg-left';
                    let metaName = isMe ? 'Bạn' : msg.user.name;
                    
                    genHtml += `
                        <div class="${alignClass} msg-bubble">
                            <div class="msg-meta fw-bold">${metaName} <span class="fw-normal ms-1" style="font-size:0.65rem">${time}</span></div>
                            <div>${msg.message}</div>
                        </div>`;
                }
            });

            const genBox = document.getElementById('general-list');
            const annBox = document.getElementById('announcement-list');

            if(genBox.innerHTML !== genHtml) { genBox.innerHTML = genHtml; genBox.scrollTop = genBox.scrollHeight; }
            if(annBox.innerHTML !== annHtml) { annBox.innerHTML = annHtml; annBox.scrollTop = annBox.scrollHeight; }
        }

        function sendMessage() {
            let inputEl = document.getElementById('chat-input');
            let message = inputEl.value.trim();
            if(!message) return;

            fetch(sendUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({ message, type: currentTab })
            }).then(res => res.json()).then(() => { inputEl.value = ''; loadMessages(); });
        }

        function handleEnter(e) { if(e.key === 'Enter') sendMessage(); }

        document.addEventListener('DOMContentLoaded', () => {
            loadMessages();
            setInterval(loadMessages, 3000); 
        });
    </script>
    @endpush

</x-layouts.teacher>