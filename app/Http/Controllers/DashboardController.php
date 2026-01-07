<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ExamAttempt;
use App\Models\User; // [MỚI] Thêm dòng này để đếm số học sinh
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * HÀM ĐIỀU HƯỚNG CHÍNH
     * Route: /dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Nếu là Admin hoặc Giáo viên -> Chuyển sang Dashboard Giáo viên
        if ($user->role === 'admin' || $user->role === 'teacher') {
            return $this->teacherDashboard(); 
        }

        // Mặc định là Học sinh -> Hiển thị Dashboard Học sinh
        return $this->studentDashboard();
    }

    /**
     * DASHBOARD CHO GIÁO VIÊN
     */
    public function teacherDashboard()
    {
        $userId = Auth::id();

        // 1. Thống kê số liệu
        // Đếm đề thi do CHÍNH giáo viên này tạo
        $totalExams = Exam::where('creator_id', $userId)->count();
        
        // Đếm tổng câu hỏi trong kho (Tài nguyên chung)
        $totalQuestions = Question::count(); 
        
        // Đếm số học sinh (Toàn hệ thống)
        $totalStudents = User::where('role', 'student')->count();

        // 2. Lấy danh sách 5 đề thi mới nhất CỦA GIÁO VIÊN NÀY
        // Dùng take(5)->get() thay vì paginate() vì dashboard chỉ cần hiện list ngắn gọn
        $recentExams = Exam::where('creator_id', $userId)
                           ->latest()
                           ->take(5)
                           ->get();

        // Trả về View với tên biến khớp với file blade 'teacher/dashboard.blade.php'
        return view('teacher.dashboard', compact('totalExams', 'totalQuestions', 'totalStudents', 'recentExams'));
    }

    /**
     * DASHBOARD CHO HỌC SINH
     */
    public function studentDashboard()
    {
        // Lấy danh sách kỳ thi chính thức đang mở
        $officialSessions = ExamSession::with('exam')
            ->where('end_at', '>', now()) // Chưa hết hạn
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard', compact('officialSessions'));
    }

    // ---------------------------------------------------------
    // CÁC HÀM KHÁC (Giữ nguyên logic cũ của bạn)
    // ---------------------------------------------------------

    public function practiceList()
    {
        $practiceExams = Exam::with('topic')->latest()->get();
        return view('practice', compact('practiceExams'));
    }
    
    public function documents()
    {
        return view('documents'); 
    }

    public function history()
    {
        $userId = Auth::id();

        // 1. Lấy dữ liệu gốc
        $attempts = ExamAttempt::with(['exam', 'examSession'])
            ->where('user_id', $userId)
            ->orderBy('submitted_at', 'asc')
            ->get();

        // 2. Tách dữ liệu thi thật
        $examAttempts = $attempts->filter(function ($item) {
            return !empty($item->exam_session_id) && $item->exam_session_id != 0;
        })->sortByDesc('submitted_at');

        // 3. Tách dữ liệu luyện tập
        $rawPracticeAttempts = $attempts->filter(function ($item) {
            return empty($item->exam_session_id) || $item->exam_session_id == 0;
        });

        // 4. Gom nhóm luyện tập
        $practiceHistory = [];
        $chartDataByExam = [];
        $grouped = $rawPracticeAttempts->groupBy('exam_id');

        foreach ($grouped as $examId => $listAttempts) {
            $examInfo = $listAttempts->first()->exam;
            $examTitle = $examInfo ? $examInfo->title : 'Đề thi đã xóa';
            
            $latestAttempt = $listAttempts->sortByDesc('submitted_at')->first();

            if ($latestAttempt) {
                $practiceHistory[] = [
                    'exam_id' => $examId,
                    'title' => $examTitle,
                    'count' => $listAttempts->count(),
                    'best_score' => $listAttempts->max('total_score'),
                    'average_score' => $listAttempts->avg('total_score'),
                    'latest_id' => $latestAttempt->id,
                    'latest_score' => $latestAttempt->total_score,
                    'latest_at' => $latestAttempt->submitted_at,
                ];

                $chartDataByExam[$examId] = $listAttempts->map(function ($item) {
                    return [
                        'date' => Carbon::parse($item->submitted_at)->format('d/m H:i'),
                        'score' => $item->total_score
                    ];
                })->values();
            }
        }

        // 5. Thống kê biểu đồ
        $totalExamsAvailable = Exam::count();
        $examsTakenCount = $grouped->count();

        $barChartData = $examAttempts->take(10)->map(function ($item) {
            return [
                'label' => $item->examSession->title ?? 'Kỳ thi',
                'score' => $item->total_score
            ];
        })->values();

        return view('history', compact(
            'examAttempts',
            'practiceHistory',
            'totalExamsAvailable',
            'examsTakenCount',
            'barChartData',
            'chartDataByExam'
        ));
    }
}