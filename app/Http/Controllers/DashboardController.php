<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\Exam;
use App\Models\ExamSession;
use Carbon\Carbon;
use App\Models\ExamAttempt;


class DashboardController extends Controller
{
    // Màn hình chính cho Giáo viên
public function teacherDashboard()
    {
        // 1. Các số liệu thống kê (Code cũ)
        $questionCount = Question::count();
        $examCount = Exam::count();
        $sessionCount = ExamSession::count();

        // 2. THÊM DÒNG NÀY: Lấy danh sách 5 đề thi mới nhất để hiển thị ra bảng "$myExams"
        // Nếu không muốn giới hạn 5 thì dùng Exam::latest()->get();
        // Bỏ "with('topic')" đi
$myExams = Exam::latest()->take(5)->get(); 

        // 3. Truyền thêm 'myExams' vào compact
        return view('teacher.dashboard', compact('questionCount', 'examCount', 'sessionCount', 'myExams'));
    }

    // Màn hình chính cho Học sinh
// 1. KỲ THI (DASHBOARD) - Chỉ lấy kỳ thi chính thức
// 1. KỲ THI (Trang chủ) - Giữ nguyên view 'dashboard'
public function studentDashboard()
{
    $userEmail = Auth::user()->email;
    $now = now();

    $officialSessions = ExamSession::whereHas('students', function($q) use ($userEmail) {
        $q->where('student_email', $userEmail);
    })
    // LOGIC MỚI: Chỉ cần chưa hết hạn là hiện (Bao gồm Sắp tới + Đang diễn ra)
    ->where('end_at', '>=', $now)
    ->orderBy('start_at', 'asc') // Sắp xếp kỳ thi gần nhất lên đầu
    ->with('exam')
    ->get();

    return view('dashboard', compact('officialSessions'));
}

    // 2. ĐỀ THI - Trỏ về view 'practice' (nằm ở resources/views/practice.blade.php)
    public function practiceList()
    {
        $practiceExams = Exam::with('topic')->latest()->get();
        // SỬA: bỏ 'student.' đi
        return view('practice', compact('practiceExams'));
    }

    // 3. LỊCH SỬ - Trỏ về view 'history' (nằm ở resources/views/history.blade.php)
public function history()
    {
        // 1. Lấy toàn bộ dữ liệu (Eager load để tránh query N+1)
        $attempts = ExamAttempt::with(['exam', 'examSession'])
            ->where('user_id', Auth::id())
            ->orderBy('submitted_at', 'desc')
            ->get();

        // 2. Tách danh sách A: Kỳ thi chính thức (Có session_id và khác 0)
        $examAttempts = $attempts->filter(function ($item) {
            return !empty($item->exam_session_id) && $item->exam_session_id != 0;
        });

        // 3. Tách danh sách B: Luyện tập (Không có session_id hoặc bằng 0)
        $practiceAttempts = $attempts->filter(function ($item) {
            return empty($item->exam_session_id) || $item->exam_session_id == 0;
        });

        // 4. Trả về View với 2 biến mới (QUAN TRỌNG)
        return view('history', compact('examAttempts', 'practiceAttempts'));
    }

    // 4. TÀI LIỆU - Trỏ về view 'documents'
    public function documents()
    {
        $documents = []; // Tạm thời rỗng
        // SỬA: bỏ 'student.' đi
        return view('documents', compact('documents'));
    }
}