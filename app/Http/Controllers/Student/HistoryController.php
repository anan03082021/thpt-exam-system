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
use App\Models\AttemptAnswer; // Import thêm model này

class HistoryController extends Controller
{
public function index()
    {
        $userId = Auth::id();

        // --- 1. SỐ LIỆU TỔNG QUAN (ĐÃ SỬA LOGIC) ---
        // SỬA: Đếm số lượng ĐỀ THI (Exam) duy nhất user đã làm
        // (Thay vì đếm ExamSession - ca thi như cũ)
        $examsTakenCount = ExamAttempt::where('user_id', $userId)
            ->distinct('exam_id') // Quan trọng: Chỉ đếm các mã đề thi khác nhau
            ->count('exam_id');

        // SỬA: Tổng số ĐỀ THI (Exam) có trong hệ thống
        // (Thay vì đếm ExamSession - ca thi như cũ)
        // Bạn cần đảm bảo đã import App\Models\Exam ở đầu file
        $totalExamsAvailable = \App\Models\Exam::count(); 

        // --- 2. LẤY TOÀN BỘ LỊCH SỬ ---
        $allAttempts = ExamAttempt::with(['examSession', 'exam'])
            ->where('user_id', $userId)
            ->whereHas('exam') // Lọc bỏ bài thi rác
            ->orderBy('submitted_at', 'desc')
            ->get();

        // A. TÁCH KỲ THI CHÍNH THỨC
        $examAttempts = $allAttempts->filter(function ($attempt) {
            return !empty($attempt->exam_session_id) && $attempt->exam_session_id != 0;
        });

        // B. TÁCH VÀ XỬ LÝ DỮ LIỆU LUYỆN TẬP
        $rawPractice = $allAttempts->filter(function ($attempt) {
            return empty($attempt->exam_session_id) || $attempt->exam_session_id == 0;
        });

        $practiceAttempts = $rawPractice; // Giữ biến này cho View cũ
        $practiceHistory = []; 
        
        if ($rawPractice->count() > 0) {
            $groupedPractice = $rawPractice->groupBy('exam_id');
            foreach ($groupedPractice as $examId => $attempts) {
                $exam = $attempts->first()->exam;
                if (!$exam) continue; 
                
                $practiceHistory[] = [
                    'title' => $exam->title ?? 'Bài luyện tập',
                    'count' => $attempts->count(),
                    'best_score' => $attempts->max('total_score'),
                    'average_score' => $attempts->avg('total_score'),
                    'latest_at' => $attempts->first()->submitted_at,
                    'latest_id' => $attempts->first()->id,
                ];
            }
        }

        // --- 3. DỮ LIỆU BIỂU ĐỒ RADAR (TÍNH % VÀ RAW DATA) ---
        $radarData = $this->calculateRadarData($userId);
        $chartLabels = $radarData['labels'];
        $chartPercent = $radarData['percents'];
        $chartRaw = $radarData['raws'];

        // --- 4. DỮ LIỆU BIỂU ĐỒ CỘT (PHỔ ĐIỂM) ---
        $barChartData = $examAttempts->take(10)->map(function ($attempt) {
            return [
                'label' => Str::limit($attempt->examSession->title ?? 'Bài thi #' . $attempt->id, 15),
                'score' => $attempt->total_score ?? 0
            ];
        })->reverse()->values();

        return view('history', compact(
            'examsTakenCount',      // Đã sửa logic
            'totalExamsAvailable',  // Đã sửa logic
            'examAttempts',
            'practiceAttempts',
            'practiceHistory',
            'chartLabels',
            'chartPercent',
            'chartRaw',
            'barChartData'
        ));
    }

    /**
     * Hàm tính toán dữ liệu cho biểu đồ Radar
     * Trả về mảng chứa: Labels (Tên), Percents (%), Raws (5/10)
     */
    private function calculateRadarData($userId)
    {
        // 1. Lấy tất cả câu trả lời của user kèm thông tin chủ đề
        // Sử dụng Eloquent để code gọn gàng hơn, hoặc Query Builder như cũ cũng được.
        // Ở đây dùng Query Builder cho hiệu suất cao giống code cũ của bạn.
        
        $stats = DB::table('attempt_answers')
            ->join('questions', 'attempt_answers.question_id', '=', 'questions.id')
            ->join('exam_attempts', 'attempt_answers.attempt_id', '=', 'exam_attempts.id')
            ->join('topics', 'questions.topic_id', '=', 'topics.id') // Join thêm bảng topics để lấy tên
            ->where('exam_attempts.user_id', $userId)
            ->select(
                'topics.name as topic_name',
                DB::raw('COUNT(*) as total_attempted'),
                DB::raw('SUM(CASE WHEN attempt_answers.is_correct = 1 THEN 1 ELSE 0 END) as total_correct')
            )
            ->groupBy('questions.topic_id', 'topics.name')
            ->get();

        $labels = [];
        $percents = [];
        $raws = [];

        // Nếu user chưa làm bài nào, lấy danh sách chủ đề rỗng để vẽ biểu đồ trống cho đẹp
        if ($stats->isEmpty()) {
            $allTopics = Topic::pluck('name')->toArray();
            foreach ($allTopics as $name) {
                $labels[] = $name;
                $percents[] = 0;
                $raws[] = "0/0";
            }
        } else {
            foreach ($stats as $stat) {
                $labels[] = $stat->topic_name;
                
                // Tính %: (Số câu đúng / Tổng số câu) * 100
                if ($stat->total_attempted > 0) {
                    $percent = ($stat->total_correct / $stat->total_attempted) * 100;
                    $percents[] = round($percent, 2);
                    $raws[] = $stat->total_correct . '/' . $stat->total_attempted;
                } else {
                    $percents[] = 0;
                    $raws[] = "0/0";
                }
            }
        }

        return [
            'labels' => $labels,
            'percents' => $percents,
            'raws' => $raws
        ];
    }
}