<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // 1. Hiển thị giao diện
    public function index()
    {
        return view('forum.index');
    }

    // 2. API lấy tin nhắn (Dùng cho AJAX Polling)
    public function fetchMessages()
    {
        // Lấy 50 tin nhắn gần nhất
        $messages = ChatMessage::with('user')
            ->orderBy('created_at', 'asc') // Cũ nhất ở trên, mới nhất ở dưới
            ->limit(100)
            ->get();
            
        return response()->json($messages);
    }

    // 3. API Gửi tin nhắn
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'required|in:general,announcement'
        ]);

        $user = Auth::user();

        // Bảo mật: Nếu là thông báo, chỉ Giáo viên/Admin được gửi
        if ($request->type === 'announcement') {
            if ($user->role === 'student') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $chat = ChatMessage::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'type' => $request->type
        ]);

        return response()->json(['status' => 'Message Sent!', 'data' => $chat->load('user')]);
    }
}