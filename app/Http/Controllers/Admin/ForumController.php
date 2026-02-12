<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage; // <--- SỬ DỤNG MODEL CỦA BẠN

class ForumController extends Controller
{
    public function index()
    {
        // Lấy tin nhắn từ bảng chat_messages, kèm thông tin user
        $messages = ChatMessage::with('user')->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.forum.index', compact('messages'));
    }

    public function destroy($id)
    {
        ChatMessage::destroy($id);
        return redirect()->back()->with('success', 'Đã xóa tin nhắn vi phạm.');
    }
}