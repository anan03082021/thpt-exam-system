<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ExamAttempt;
use App\Models\Topic;
use App\Models\ExamSession;

class HistoryController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // --- 1. SỐ LIỆU TỔNG QUAN (Cho biểu đồ tròn) ---
        $examsTakenCount = ExamAttempt::where('user_id', $userId)
            ->whereNotNull('exam_session_id')
            ->distinct('exam_session_id')
            ->count();

        $totalExamsAvailable = ExamSession::count(); 

        // --- 2. LẤY TOÀN BỘ LỊCH SỬ ĐỂ PHÂN LOẠI ---
        $allAttempts = ExamAttempt::with(['examSession', 'exam'])
            ->where('user_id', $userId)
            ->orderBy('submitted_at', 'desc')
            ->get();

        // A. TÁCH KỲ THI CHÍNH THỨC (Session ID khác 0 và không null)
        $examAttempts = $allAttempts->filter(function ($attempt) {
            return !empty($attempt->exam_session_id) && $attempt->exam_session_id != 0;
        });

        // B. TÁCH VÀ XỬ LÝ DỮ LIỆU LUYỆN TẬP (Session ID = 0 hoặc null)
        $rawPractice = $allAttempts->filter(function ($attempt) {
            return empty($attempt->exam_session_id) || $attempt->exam_session_id == 0;
        });

        // Nhóm các bài luyện tập theo Đề thi (Exam ID) để thống kê
        $practiceHistory = [];
        if ($rawPractice->count() > 0) {
            $groupedPractice = $rawPractice->groupBy('exam_id');
            
            foreach ($groupedPractice as $examId => $attempts) {
                $exam = $attempts->first()->exam;
                $practiceHistory[] = [
                    'title' => $exam->title ?? 'Bài luyện tập',
                    'count' => $attempts->count(), // Số lần làm
                    'best_score' => $attempts->max('total_score'), // Điểm cao nhất
                    'average_score' => $attempts->avg('total_score'), // Điểm trung bình
                    'latest_at' => $attempts->first()->submitted_at, // Ngày làm mới nhất
                    'latest_id' => $attempts->first()->id, // ID bài làm mới nhất (để xem chi tiết)
                ];
            }
        }

        // --- 3. DỮ LIỆU BIỂU ĐỒ RADAR (MỨC ĐỘ THÀNH THẠO) ---
        $topicMastery = $this->calculateTopicMastery($userId);

        // --- 4. DỮ LIỆU BIỂU ĐỒ CỘT (PHỔ ĐIỂM THI THẬT) ---
        // Lấy 10 bài thi thật gần nhất
        $barChartData = $examAttempts->take(10)->map(function ($attempt) {
            return [
                'label' => Str::limit($attempt->examSession->title ?? 'Bài thi #' . $attempt->id, 15),
                'score' => $attempt->total_score ?? 0
            ];
        })->reverse()->values();

        return view('history', compact(
            'examsTakenCount',
            'totalExamsAvailable',
            'examAttempts',     // Dữ liệu Tab 1
            'practiceHistory',  // Dữ liệu Tab 2 (Đã xử lý)
            'topicMastery',     // Dữ liệu Radar Chart
            'barChartData'      // Dữ liệu Bar Chart
        ));
    }

    // Hàm phụ: Tính điểm kỹ năng theo chủ đề
    private function calculateTopicMastery($userId)
    {
        $allTopics = Topic::pluck('name', 'id'); 

        // Lưu ý: Sửa 'attempt_answers.attempt_id' cho khớp với DB của bạn
        $stats = DB::table('attempt_answers')
            ->join('questions', 'attempt_answers.question_id', '=', 'questions.id')
            ->join('exam_attempts', 'attempt_answers.attempt_id', '=', 'exam_attempts.id') 
            ->where('exam_attempts.user_id', $userId)
            ->select(
                'questions.topic_id',
                DB::raw('COUNT(*) as total_attempted'), 
                DB::raw('SUM(CASE WHEN attempt_answers.is_correct = 1 THEN 1 ELSE 0 END) as total_correct')
            )
            ->groupBy('questions.topic_id')
            ->get()
            ->keyBy('topic_id');

        $mastery = [];
        foreach ($allTopics as $topicId => $topicName) {
            $score = 0;
            if (isset($stats[$topicId])) {
                $data = $stats[$topicId];
                if ($data->total_attempted > 0) {
                    $score = ($data->total_correct / $data->total_attempted) * 10;
                }
            }
            $mastery[] = [
                'name' => $topicName,
                'score' => round($score, 2)
            ];
        }
        return $mastery;
    }
}