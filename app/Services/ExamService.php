<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\AttemptAnswer;
use Illuminate\Support\Facades\DB;

class ExamService
{
    /**
     * Chức năng 1: Tính điểm bài thi
     * Logic: 
     * - Dạng 1 (4 chọn 1): Đúng +0.25
     * - Dạng 2 (Đúng/Sai chùm): Gom nhóm các ý con, đếm số ý đúng để quy ra điểm.
     */
    public function calculateScore($attemptId)
    {
        $attempt = ExamAttempt::with('attemptAnswers.question')->find($attemptId);
        $totalScore = 0;

        // 1. Lấy tất cả câu trả lời của thí sinh
        $answers = $attempt->attemptAnswers;

        // 2. Tách câu hỏi thành 2 nhóm
        // Nhóm A: Câu độc lập (Dạng 1)
        $singleQuestions = $answers->filter(function ($ans) {
            return $ans->question->type === 'single_choice';
        });

        // Nhóm B: Câu thuộc chùm (Dạng 2) - Group by parent_id để xử lý cả chùm
        $groupQuestions = $answers->filter(function ($ans) {
            return $ans->question->type === 'true_false_item';
        })->groupBy(function ($ans) {
            return $ans->question->parent_id;
        });

        // 3. Xử lý tính điểm Dạng 1: Mỗi câu đúng +0.25
        foreach ($singleQuestions as $ans) {
            if ($ans->is_correct) {
                $totalScore += 0.25;
            }
        }

        // 4. Xử lý tính điểm Dạng 2: Theo thang điểm đặc biệt
        foreach ($groupQuestions as $parentId => $groupAnswers) {
            // Đếm số ý trả lời đúng trong 1 câu chùm
            $correctCount = $groupAnswers->where('is_correct', true)->count();
            
            // Áp dụng ma trận điểm 
            switch ($correctCount) {
                case 1: $totalScore += 0.10; break;
                case 2: $totalScore += 0.25; break;
                case 3: $totalScore += 0.50; break;
                case 4: $totalScore += 1.00; break;
                default: $totalScore += 0;
            }
        }

        // 5. Cập nhật điểm vào DB
        $attempt->update(['total_score' => $totalScore]);
        
        return $totalScore;
    }

    /**
     * Chức năng 2: Gợi ý ôn tập
     * Logic: Tìm các câu sai -> Lấy Topic -> Đếm số lượng -> Trả về lời khuyên.
     */
    public function getReviewSuggestions($attemptId)
    {
        // Query lấy các Topic có câu trả lời SAI
        $weakTopics = DB::table('attempt_answers')
            ->join('questions', 'attempt_answers.question_id', '=', 'questions.id')
            ->join('topics', 'questions.topic_id', '=', 'topics.id')
            ->where('attempt_answers.attempt_id', $attemptId)
            ->where('attempt_answers.is_correct', false) // Chỉ lấy câu sai
            ->select('topics.name', DB::raw('count(*) as wrong_count'))
            ->groupBy('topics.id', 'topics.name')
            ->orderByDesc('wrong_count')
            ->get();

        $suggestions = [];
        foreach ($weakTopics as $topic) {
            $suggestions[] = "Bạn đã làm sai {$topic->wrong_count} câu thuộc chủ đề '{$topic->name}'. Hãy ôn tập lại chương này.";
        }

        if (empty($suggestions)) {
            return ["Chúc mừng! Bạn đã làm đúng tất cả các câu hỏi."];
        }

        return $suggestions;
    }
}