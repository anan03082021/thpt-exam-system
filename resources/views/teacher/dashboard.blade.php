<x-layouts.teacher>
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }

        /* BANNER */
        .dashboard-banner {
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%) !important; /* Màu tối quyền lực cho Admin */
            color: white !important;
            border-radius: 20px; padding: 2rem 2.5rem; margin-bottom: 2rem;
            position: relative; overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3);
        }
        .banner-deco {
            position: absolute; opacity: 0.1; right: -30px; bottom: -50px;
            font-size: 8rem; color: white; transform: rotate(-15deg);
        }

        /* STATS CARD */
        .stat-card {
            background: white; border-radius: 16px; padding: 1.5rem;
            border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            transition: transform 0.2s; display: flex; align-items: center; justify-content: space-between;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        .stat-icon {
            width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }

        /* CHAT WIDGET (Compact & Modern) */
        .forum-section { height: 320px; margin-bottom: 2.5rem; } /* Cao hơn chút so với HS để dễ quản lý */
        
        .modern-card {
            background: white; border-radius: 16px; border: 1px solid #f1f5f9;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03); height: 100%; display: flex; flex-direction: column; overflow: hidden;
        }

        .chat-header {
            padding: 10px 15px; font-size: 0.85rem; font-weight: 700;
            display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9;
        }
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

        .msg-bubble {
            padding: 8px 14px; border-radius: 18px; line-height: 1.4; position: relative; word-wrap: break-word;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .msg-left .msg-bubble { background: #f1f5f9; color: #334155; border-bottom-left-radius: 4px; }
        /* Admin chat màu đậm hơn */
        .msg-right .msg-bubble { background: #0f172a; color: white; border-bottom-right-radius: 4px; }

        .msg-info { font-size: 0.7rem; color: #94a3b8; margin-bottom: 3px; font-weight: 500; }

        /* Bong bóng thông báo */
        .announcement-bubble { 
            background: #fff1f2; border: 1px solid #fecdd3; color: #881337; 
            padding: 10px 12px; border-radius: 12px; font-size: 0.85rem;
        }

        /* Input */
        .input-area { padding: 10px; background: white; border-top: 1px solid #f1f5f9; display: flex; gap: 8px; align-items: center; }
        .chat-input {
            flex: 1; border: 1px solid #e2e8f0; border-radius: 24px; padding: 8px 16px;
            font-size: 0.85rem; outline: none; transition: all 0.2s; background: #f8fafc;
        }
        .chat-input:focus { border-color: #0f172a; background: white; }
        .btn-send {
            width: 36px; height: 36px; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center;
            color: white; cursor: pointer; transition: transform 0.2s;
        }
        .btn-send:hover { transform: scale(1.05); }
        .btn-send.general { background: #0f172a; }
        .btn-send.announcement { background: #dc2626; }
    </style>
    @endpush

    <div class="container mt-3">
        @if(session('error') || session('success'))
            <div class="alert {{ session('error') ? 'alert-danger' : 'alert-success' }} py-2 fs-6 shadow-sm border-0 rounded-3 mb-4">
                {{ session('error') ?? session('success') }}
            </div>
        @endif
    </div>

    <div class="container pb-5">
        {{-- 1. BANNER --}}
        <div class="dashboard-banner d-flex align-items-center justify-content-between">
            <div class="position-relative z-1">
                <h2 class="fw-bold mb-2">Trang Quản Trị Viên</h2>
                <p class="mb-0 opacity-75">Quản lý hệ thống thi trắc nghiệm trực tuyến.</p>
            </div>
            <i class="bi bi-shield-check banner-deco"></i>
        </div>

        {{-- 2. THỐNG KÊ NHANH --}}
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Tổng học sinh</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ $totalStudents ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon bg-blue-50 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Ngân hàng đề</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ $totalExams ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon bg-indigo-50 text-indigo">
                        <i class="bi bi-journal-album"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Ca thi đã tạo</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ $totalSessions ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon bg-green-50 text-success">
                        <i class="bi bi-broadcast"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. DIỄN ĐÀN (DÀNH CHO GIÁO VIÊN) --}}
        <div class="d-flex align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-chat-square-quote-fill me-2 text-danger"></i>Trung tâm Thông báo & Thảo luận</h5>
        </div>

        <div class="forum-section row g-3">
            {{-- CỘT THÔNG BÁO (GIÁO VIÊN ĐƯỢC PHÉP NHẬP) --}}
            <div class="col-lg-4 h-100">
                <div class="modern-card">
                    <div class="chat-header announcement">
                        <span><i class="bi bi-megaphone-fill me-2"></i> KÊNH THÔNG BÁO</span>
                        <span class="badge bg-danger text-white border">Admin Only</span>
                    </div>
                    
                    <div class="messages-area" id="announcement-list">
                        {{-- JS load nội dung --}}
                    </div>

                    {{-- Giáo viên CÓ ô nhập liệu --}}
                    <div class="input-area">
                        <input type="text" id="announcement-input" class="chat-input" placeholder="Đăng thông báo quan trọng..." onkeypress="handleEnter(event, 'announcement')">
                        <button class="btn-send announcement" onclick="sendMessage('announcement')"><i class="bi bi-send-fill fs-6"></i></button>
                    </div>
                </div>
            </div>

            {{-- CỘT CHAT CHUNG --}}
            <div class="col-lg-8 h-100">
                <div class="modern-card">
                    <div class="chat-header general">
                        <span><i class="bi bi-people-fill me-2"></i> THẢO LUẬN CHUNG</span>
                        <span class="badge bg-white text-dark border fw-normal">Trực tuyến</span>
                    </div>
                    
                    <div class="messages-area" id="general-list">
                        {{-- JS load nội dung --}}
                    </div>

                    <div class="input-area">
                        <input type="text" id="general-input" class="chat-input" placeholder="Trả lời học sinh..." onkeypress="handleEnter(event, 'general')">
                        <button class="btn-send general" onclick="sendMessage('general')"><i class="bi bi-send-fill fs-6"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS (DÙNG CHUNG LOGIC VỚI HỌC SINH) --}}
    @push('scripts')
    <script>
        const currentUserId = {{ Auth::id() }};
        const fetchUrl = "{{ route('forum.fetch') }}";
        const sendUrl = "{{ route('forum.send') }}";

        function loadMessages() {
            fetch(fetchUrl).then(res => res.json()).then(data => renderMessages(data)).catch(err => console.error(err));
        }

        function renderMessages(messages) {
            let generalHtml = '';
            let announcementHtml = '';

            messages.forEach(msg => {
                let time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                // Render Thông báo
                if(msg.type === 'announcement') {
                    // Nút xóa (chỉ hiển thị demo, cần thêm API xóa nếu muốn)
                    let deleteBtn = ''; 
                    
                    announcementHtml += `
                        <div class="announcement-item mb-2">
                            <div class="msg-info fw-bold text-danger d-flex justify-content-between">
                                <span>${msg.user.name} • ${time}</span>
                            </div>
                            <div class="announcement-bubble">${msg.message}</div>
                        </div>`;
                } else {
                    let isMe = msg.user_id === currentUserId;
                    let align = isMe ? 'msg-right' : 'msg-left';
                    let name = isMe ? 'Bạn (GV)' : msg.user.name;
                    generalHtml += `
                        <div class="msg-item ${align} mb-2">
                            <div class="msg-info">${name}</div>
                            <div class="msg-bubble">${msg.message}</div>
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
        });
    </script>
    @endpush
</x-layouts.teacher>