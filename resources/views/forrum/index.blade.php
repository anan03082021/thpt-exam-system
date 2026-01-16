<x-app-layout>
    @push('styles')
    <style>
        .chat-container { height: calc(100vh - 180px); min-height: 500px; display: flex; gap: 20px; }
        
        /* CỘT THÔNG BÁO (BÊN TRÁI) */
        .announcement-box {
            flex: 1;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            display: flex; flex-direction: column;
            overflow: hidden;
            border-top: 4px solid #ef4444; /* Màu đỏ cho thông báo */
        }

        /* CỘT CHAT CHUNG (BÊN PHẢI) */
        .general-box {
            flex: 2;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            display: flex; flex-direction: column;
            overflow: hidden;
            border-top: 4px solid #4f46e5; /* Màu xanh cho chat chung */
        }

        .box-header {
            padding: 15px; border-bottom: 1px solid #f3f4f6; font-weight: bold; background: #f9fafb;
            display: flex; justify-content: space-between; align-items: center;
        }

        .messages-area {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f9fafb;
            display: flex; flex-direction: column; gap: 15px;
        }

        /* TIN NHẮN STYLE */
        .msg-item { display: flex; flex-direction: column; max-width: 85%; }
        .msg-left { align-self: flex-start; }
        .msg-right { align-self: flex-end; align-items: flex-end; }

        .msg-bubble {
            padding: 10px 15px; border-radius: 12px; position: relative; word-wrap: break-word;
            font-size: 0.95rem; line-height: 1.5;
        }
        
        .msg-left .msg-bubble { background: #e5e7eb; color: #1f2937; border-bottom-left-radius: 2px; }
        .msg-right .msg-bubble { background: #4f46e5; color: white; border-bottom-right-radius: 2px; }
        
        /* Tin nhắn thông báo đặc biệt */
        .announcement-msg .msg-bubble {
            background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; width: 100%;
        }

        .msg-info { font-size: 0.75rem; margin-bottom: 4px; color: #6b7280; }
        .msg-right .msg-info { text-align: right; }

        /* INPUT AREA */
        .input-area { padding: 15px; background: white; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; }
        .chat-input {
            flex: 1; border: 1px solid #d1d5db; border-radius: 20px; padding: 10px 15px; outline: none; transition: border 0.2s;
        }
        .chat-input:focus { border-color: #4f46e5; }
        .btn-send {
            background: #4f46e5; color: white; border: none; padding: 0 20px; border-radius: 20px; font-weight: bold; cursor: pointer;
        }
        .btn-send:hover { background: #4338ca; }
        .btn-send:disabled { background: #9ca3af; cursor: not-allowed; }

    </style>
    @endpush

    <div class="container py-4">
        <div class="chat-container">
            
            {{-- 1. KÊNH THÔNG BÁO (Chỉ giáo viên chat) --}}
            <div class="announcement-box shadow-sm">
                <div class="box-header text-danger">
                    <span><i class="bi bi-megaphone-fill me-2"></i> KÊNH THÔNG BÁO</span>
                </div>
                <div class="messages-area" id="announcement-list">
                    {{-- JS sẽ load tin nhắn vào đây --}}
                </div>
                
                {{-- Chỉ hiện ô nhập liệu nếu là Giáo viên/Admin --}}
                @if(Auth::user()->role !== 'student')
                    <div class="input-area">
                        <input type="text" id="announcement-input" class="chat-input" placeholder="Viết thông báo quan trọng...">
                        <button class="btn-send bg-danger" onclick="sendMessage('announcement')">GỬI</button>
                    </div>
                @else
                    <div class="p-3 text-center text-muted small bg-light border-top">
                        <i class="bi bi-lock-fill"></i> Chỉ giáo viên mới được đăng tin.
                    </div>
                @endif
            </div>

            {{-- 2. KÊNH THẢO LUẬN CHUNG (Ai cũng chat được) --}}
            <div class="general-box shadow-sm">
                <div class="box-header text-primary">
                    <span><i class="bi bi-people-fill me-2"></i> THẢO LUẬN CHUNG</span>
                    <small class="text-muted fw-normal">Real-time chat</small>
                </div>
                <div class="messages-area" id="general-list">
                    {{-- JS sẽ load tin nhắn vào đây --}}
                </div>
                <div class="input-area">
                    <input type="text" id="general-input" class="chat-input" placeholder="Nhập tin nhắn..." onkeypress="handleEnter(event, 'general')">
                    <button class="btn-send" onclick="sendMessage('general')"><i class="bi bi-send-fill"></i></button>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        const currentUserId = {{ Auth::id() }};
        const fetchUrl = "{{ route('forum.fetch') }}";
        const sendUrl = "{{ route('forum.send') }}";
        let lastMsgCount = 0; // Để check xem có tin mới không

        // 1. Hàm gửi tin nhắn
        function sendMessage(type) {
            let inputId = type === 'general' ? 'general-input' : 'announcement-input';
            let inputEl = document.getElementById(inputId);
            let message = inputEl.value.trim();

            if (!message) return;

            // Gửi AJAX
            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: message, type: type })
            })
            .then(response => response.json())
            .then(data => {
                inputEl.value = ''; // Xóa ô nhập
                loadMessages(); // Load lại ngay lập tức
            })
            .catch(error => console.error('Error:', error));
        }

        // 2. Hàm xử lý nhấn Enter
        function handleEnter(e, type) {
            if (e.key === 'Enter') sendMessage(type);
        }

        // 3. Hàm tải tin nhắn (Chạy định kỳ)
        function loadMessages() {
            fetch(fetchUrl)
            .then(response => response.json())
            .then(messages => {
                // Render General
                renderChannel(messages.filter(m => m.type === 'general'), 'general-list');
                // Render Announcement
                renderChannel(messages.filter(m => m.type === 'announcement'), 'announcement-list');
            });
        }

        // 4. Render HTML ra màn hình
        function renderChannel(messages, elementId) {
            let container = document.getElementById(elementId);
            
            // Lưu vị trí scroll hiện tại để xem có đang ở đáy không
            let isAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;

            let html = '';
            messages.forEach(msg => {
                let isMe = msg.user_id === currentUserId;
                let alignClass = isMe ? 'msg-right' : 'msg-left';
                let time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                
                // Style cho Thông báo khác biệt chút
                if (elementId === 'announcement-list') {
                    html += `
                        <div class="msg-item announcement-msg mb-3">
                            <div class="msg-info fw-bold text-danger">
                                <i class="bi bi-person-circle"></i> ${msg.user.name} - ${time}
                            </div>
                            <div class="msg-bubble">${msg.message}</div>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="msg-item ${alignClass}">
                            <div class="msg-info">${isMe ? 'Bạn' : msg.user.name} • ${time}</div>
                            <div class="msg-bubble">${msg.message}</div>
                        </div>
                    `;
                }
            });

            // Chỉ update DOM nếu có nội dung thay đổi (đơn giản hóa bằng cách ghi đè)
            // Trong thực tế có thể tối ưu hơn, nhưng với đồ án này ghi đè là an toàn nhất
            if (container.innerHTML !== html) {
                container.innerHTML = html;
                // Nếu đang ở đáy hoặc mới load lần đầu -> tự scroll xuống
                container.scrollTop = container.scrollHeight;
            }
        }

        // 5. Khởi chạy
        document.addEventListener('DOMContentLoaded', function() {
            loadMessages(); // Load ngay khi vào trang
            setInterval(loadMessages, 2000); // Polling mỗi 2 giây
        });

    </script>
    @endpush
</x-app-layout>