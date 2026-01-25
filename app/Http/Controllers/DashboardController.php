<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
    use App\Models\Question;
    use App\Models\Exam;
    use App\Models\ExamSession;
    use App\Models\ExamAttempt;
    use App\Models\User;
    use Carbon\Carbon;

    class DashboardController extends Controller
    {
        /**
         * HÀM ĐIỀU HƯỚNG CHÍNH (Route: /dashboard)
         * Tự động chuyển hướng dựa trên vai trò (Role)
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
         * DASHBOARD CHO GIÁO VIÊN / ADMIN
         */
        public function teacherDashboard()
        {
            $userId = Auth::id();

            // 1. Thống kê số liệu tổng quan
            // Đếm đề thi do CHÍNH giáo viên này tạo
            $totalExams = Exam::where('creator_id', $userId)->count();
            
            // Đếm tổng câu hỏi trong kho (Tài nguyên chung)
            $totalQuestions = Question::count(); 
            
            // Đếm số học sinh (Toàn hệ thống)
            $totalStudents = User::where('role', 'student')->count();

            // [MỚI] Đếm tổng số Ca thi đã tạo (Cần thiết cho giao diện mới)
            $totalSessions = ExamSession::count(); 

            // 2. Lấy danh sách 5 ca thi mới nhất để hiển thị widget
            $recentSessions = ExamSession::with('exam')
                                        ->orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();
            
            // (Tùy chọn) Lấy 5 đề thi mới nhất của giáo viên (nếu giao diện cần)
            $recentExams = Exam::where('creator_id', $userId)
                            ->latest()
                            ->take(5)
                            ->get();

            // Trả về View với đầy đủ dữ liệu
            return view('teacher.dashboard', compact(
                'totalExams', 
                'totalQuestions', 
                'totalStudents', 
                'totalSessions', // <-- Biến này quan trọng cho giao diện Admin mới
                'recentSessions',
                'recentExams'
            ));
        }

        /**
         * DASHBOARD CHO HỌC SINH
         */
        public function studentDashboard()
        {
            // Lấy danh sách kỳ thi chính thức đang mở (chưa hết hạn)
            $officialSessions = ExamSession::with('exam')
                ->where('end_at', '>', now()) 
                ->orderBy('created_at', 'desc')
                ->get();

            return view('dashboard', compact('officialSessions'));
        }

        // ---------------------------------------------------------
        // CÁC HÀM KHÁC (GIỮ NGUYÊN NHƯ CŨ)
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

            // 1. Lấy dữ liệu lịch sử làm bài
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

            // 4. Xử lý dữ liệu luyện tập để vẽ biểu đồ
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

            // 5. Thống kê tổng quan
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