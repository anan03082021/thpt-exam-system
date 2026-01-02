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
        // 1. Các số liệu thống kê
        $questionCount = Question::count();
        $examCount = Exam::count();
        $sessionCount = ExamSession::count();

        // 2. Lấy danh sách 5 đề thi mới nhất
        $myExams = Exam::latest()->take(5)->get(); 

        // 3. Truyền thêm 'myExams' vào compact
        return view('teacher.dashboard', compact('questionCount', 'examCount', 'sessionCount', 'myExams'));
    }

    // Màn hình chính cho Học sinh (Kỳ thi)
public function studentDashboard()
{
    // Lấy TẤT CẢ kỳ thi đang mở (chưa kết thúc)
    // Bao gồm cả kỳ thi cần mật khẩu và kỳ thi whitelist
    $officialSessions = \App\Models\ExamSession::with('exam')
        ->where('end_at', '>', now()) // Chỉ lấy cái chưa hết hạn
        ->orderBy('created_at', 'desc')
        ->get();

    // Truyền biến $officialSessions sang View (để khớp với code View bạn đang có)
    return view('dashboard', compact('officialSessions'));
}

    // Danh sách đề luyện tập
    public function practiceList()
    {
        $practiceExams = Exam::with('topic')->latest()->get();
        return view('practice', compact('practiceExams'));
    }

    // Lịch sử làm bài & Tiến độ
    public function history()
    {
        $userId = Auth::id();

        // 1. Lấy dữ liệu gốc
        $attempts = ExamAttempt::with(['exam', 'examSession'])
            ->where('user_id', $userId)
            ->orderBy('submitted_at', 'asc') 
            ->get();

        // 2. Tách dữ liệu
        // A. Danh sách thi thật
        $examAttempts = $attempts->filter(function ($item) {
            return !empty($item->exam_session_id) && $item->exam_session_id != 0;
        })->sortByDesc('submitted_at');

        // B. Danh sách luyện tập (CẦN GOM NHÓM)
        $rawPracticeAttempts = $attempts->filter(function ($item) {
            return empty($item->exam_session_id) || $item->exam_session_id == 0;
        });

        // --- GOM NHÓM LUYỆN TẬP ---
        $practiceHistory = [];
        $chartDataByExam = [];

        // Gom nhóm theo Exam ID
        $grouped = $rawPracticeAttempts->groupBy('exam_id');

        foreach ($grouped as $examId => $listAttempts) {
            // Lấy thông tin đề thi từ bản ghi đầu tiên
            $examInfo = $listAttempts->first()->exam;
            $examTitle = $examInfo ? $examInfo->title : 'Đề thi đã xóa';

            // --- BƯỚC SỬA LỖI QUAN TRỌNG TẠI ĐÂY ---
            // 1. Lấy bài làm mới nhất ra trước
            $latestAttempt = $listAttempts->sortByDesc('submitted_at')->first();

            // 2. Kiểm tra tồn tại để tránh lỗi
            if ($latestAttempt) {
                $practiceHistory[] = [
                    'exam_id' => $examId,
                    'title' => $examTitle,
                    'count' => $listAttempts->count(),
                    'best_score' => $listAttempts->max('total_score'),
                    'average_score' => $listAttempts->avg('total_score'), // Đã thêm dấu phẩy ở đây
                    
                    // Các trường mới thêm vào
                    'latest_id' => $latestAttempt->id,
                    'latest_score' => $latestAttempt->total_score,
                    'latest_at' => $latestAttempt->submitted_at,
                ];

                // Chuẩn bị dữ liệu cho biểu đồ
                $chartDataByExam[$examId] = $listAttempts->map(function ($item) {
                    return [
                        'date' => \Carbon\Carbon::parse($item->submitted_at)->format('d/m H:i'),
                        'score' => $item->total_score
                    ];
                })->values();
            }
        }

        // --- DỮ LIỆU THỐNG KÊ CHUNG ---
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