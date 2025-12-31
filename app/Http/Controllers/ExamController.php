<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ExamService;
use App\Models\ExamAttempt;
use App\Models\AttemptAnswer;
use App\Models\ExamSession; // <--- Mới: Dùng để check ca thi
use App\Models\Answer;
use App\Models\Exam;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    // 1. VÀO LÀM BÀI (CHECK CA THI)
    // Route: /exam/take/{sessionId}
    public function takeExam($sessionId)
    {
        // A. Lấy thông tin Ca thi
        $session = ExamSession::with(['exam.questions' => function($q) {
            // Chỉ lấy câu hỏi cha (để tránh lặp câu con trong view nếu view tự loop)
            // Kèm theo đáp án và câu con (cho dạng chùm)
            $q->whereNull('parent_id')
              ->with(['answers', 'children.answers']);
        }])->findOrFail($sessionId);

        // B. Validate: Kiểm tra thời gian
        $now = now();
        if ($now < $session->start_at) {
            return redirect()->route('student.dashboard')->with('error', 'Chưa đến giờ làm bài!');
        }
        if ($now > $session->end_at) {
            return redirect()->route('student.dashboard')->with('error', 'Đã hết giờ làm bài!');
        }

        // C. Validate: Kiểm tra Học sinh có trong danh sách không
        // (Check xem email của user đang login có nằm trong bảng exam_session_students của session này không)
        $userEmail = Auth::user()->email;
        $isAllowed = $session->students()->where('student_email', $userEmail)->exists();

        if (!$isAllowed) {
            return redirect()->route('student.dashboard')->with('error', 'Bạn không có tên trong danh sách thi này!');
        }

        // D. Kiểm tra xem đã nộp bài chưa (Nếu chỉ cho thi 1 lần)
        // Code check này tùy chọn, nếu bạn muốn cho thi lại thì bỏ qua.
        
        $exam = $session->exam;
        return view('exam.take', compact('exam', 'session'));
    }

    // 2. XỬ LÝ NỘP BÀI
    // Route: /exam/submit/{sessionId}
public function submitExam(Request $request, $sessionId)
    {
        // TRƯỜNG HỢP 1: LUYỆN TẬP (Session ID = 0)
        if ($sessionId == 0) {
            // Lấy ID đề thi từ input ẩn chúng ta vừa thêm ở Bước 1
            $examId = $request->exam_id_hidden;
            
            // Không cần kiểm tra giờ giấc
        } 
        // TRƯỜNG HỢP 2: THI CHÍNH THỨC
        else {
            $session = ExamSession::findOrFail($sessionId);
            $examId = $session->exam_id;

            // Kiểm tra thời gian nộp bài (cho phép trễ 2 phút)
            if (now() > $session->end_at->addMinutes(2)) {
                return redirect()->route('dashboard')->with('error', 'Quá giờ nộp bài!');
            }
        }

        // 1. Tạo lượt thi mới
        $attempt = ExamAttempt::create([
            'user_id' => Auth::id(),
            'exam_id' => $examId,
            'exam_session_id' => $sessionId, 
            'started_at' => now(), // (Tạm tính)
            'submitted_at' => now(),
            'total_score' => 0
        ]);

        // 2. Lưu câu trả lời
        if ($request->has('answers')) {
            foreach ($request->answers as $questionId => $answerId) {
                $answer = Answer::find($answerId);
                if ($answer) {  
                    AttemptAnswer::create([
                        'attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                        'selected_answer_id' => $answerId,
                        'is_correct' => $answer->is_correct
                    ]);
                }
            }
        }

        // 3. Tính điểm
        $this->examService->calculateScore($attempt->id);

        // 4. Chuyển hướng xem kết quả
        return redirect()->route('exam.result', $attempt->id);
    }

    // 3. XEM KẾT QUẢ
    // Route: /exam/result/{attemptId}
public function showResult($attemptId)
    {
        // SỬA LẠI TÊN QUAN HỆ Ở ĐÂY CHO KHỚP VỚI MODEL
        $attemptDetail = ExamAttempt::with([
            'exam',
            'attemptAnswers.question.topic',     // Cũ: answers -> Mới: attemptAnswers
            'attemptAnswers.question.answers',   // Cũ: answers -> Mới: attemptAnswers
            'attemptAnswers.question.parent',    // Cũ: answers -> Mới: attemptAnswers
            'attemptAnswers.selectedAnswer'      // Cũ: answers -> Mới: attemptAnswers
        ])
        ->where('id', $attemptId)
        ->where('user_id', Auth::id())
        ->firstOrFail();

        $exam = $attemptDetail->exam;
        $score = $attemptDetail->total_score;
        $suggestions = $this->examService->getReviewSuggestions($attemptDetail->id);

        return view('exam.result', compact('attemptDetail', 'exam', 'score', 'suggestions'));
    }

    // Bắt đầu làm bài luyện tập
    public function startPractice($examId)
    {
        $exam = Exam::with(['questions' => function($q) {
            $q->whereNull('parent_id')->with(['answers', 'children.answers']);
        }])->findOrFail($examId);

        // Tạo một Session giả lập (Fake Session)
        // Để tái sử dụng giao diện làm bài mà không cần sửa file view
        $session = new ExamSession();
        $session->id = 0; // ID 0 để đánh dấu là luyện tập
        $session->title = "Luyện tập: " . $exam->title;
        $session->exam_id = $exam->id;
        
        // Cài đặt thời gian: Bắt đầu ngay bây giờ, Kết thúc sau [duration] phút
        $session->start_at = now();
        $session->end_at = now()->addMinutes($exam->duration);

        return view('exam.take', compact('exam', 'session'));
    }

    public function history()
    {
        // 1. Lấy dữ liệu
        $attempts = ExamAttempt::with(['exam', 'examSession'])
            ->where('user_id', Auth::id())
            ->orderBy('submitted_at', 'desc')
            ->get();

        // 2. Phân loại: Kỳ thi chính thức
        $examAttempts = $attempts->filter(function ($item) {
            return !empty($item->exam_session_id) && $item->exam_session_id != 0;
        });

        // 3. Phân loại: Luyện tập
        $practiceAttempts = $attempts->filter(function ($item) {
            return empty($item->exam_session_id) || $item->exam_session_id == 0;
        });

        // 4. Trả về View với 2 biến mới
        return view('history', compact('examAttempts', 'practiceAttempts'));
    }
}