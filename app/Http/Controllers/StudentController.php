<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        // 1. Lấy danh sách đề thi đang mở (Published)
        // Loại bỏ các đề thi mà học sinh này đã làm rồi (nếu muốn chỉ cho thi 1 lần)
        // Ở đây tôi giữ nguyên để HS có thể thấy tất cả đề
        $availableExams = Exam::where('status', 'published')
            ->orderByDesc('created_at')
            ->get();

        // 2. Lấy lịch sử thi của học sinh hiện tại
        $myAttempts = ExamAttempt::with('exam')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard', compact('availableExams', 'myAttempts'));
    }
}