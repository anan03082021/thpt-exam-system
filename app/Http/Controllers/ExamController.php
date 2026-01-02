<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ExamService;
use App\Models\ExamAttempt;
use App\Models\AttemptAnswer;
use App\Models\ExamSession;
use App\Models\Answer;
use App\Models\Exam;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    // 1. VÀO LÀM BÀI (CHECK CA THI & MẬT KHẨU)
    // Route: /exam/take/{sessionId}
    public function takeExam($sessionId)
    {
        // A. Lấy thông tin Ca thi
        $session = ExamSession::with(['exam.questions' => function($q) {
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

        // --- LOGIC MỚI BẮT ĐẦU TỪ ĐÂY ---

        // C. Kiểm tra quyền truy cập
        $userEmail = Auth::user()->email;

        // 1. Check Whitelist: Có trong danh sách được chỉ định không?
        $isWhitelisted = $session->students()->where('student_email', $userEmail)->exists();

        // 2. Check Session Password: Đã nhập đúng mật khẩu trước đó chưa?
        // (Kiểm tra xem trong session trình duyệt có lưu key 'exam_access_ID' không)
        $sessionKey = 'exam_access_' . $sessionId;
        $hasAccessByPassword = session()->has($sessionKey);

        // D. Quyết định cho vào hay chặn
        if ($isWhitelisted || $hasAccessByPassword) {
            // -> ĐƯỢC PHÉP VÀO THI
            $exam = $session->exam;
            return view('exam.take', compact('exam', 'session'));
        }

        // E. Nếu chưa được vào, nhưng kỳ thi có mật khẩu -> Hiện form nhập pass
        if (!empty($session->password)) {
            return view('exam.password_check', compact('session'));
        }

        // F. Không có tên, không có pass -> Chặn
        return redirect()->route('student.dashboard')->with('error', 'Bạn không có quyền tham gia kỳ thi này!');
    }

    // 2. XỬ LÝ NHẬP MẬT KHẨU (MỚI THÊM)
    // Route: /exam/join/{sessionId} (POST)
    public function joinWithPassword(Request $request, $sessionId)
    {
        $session = ExamSession::findOrFail($sessionId);

        // Kiểm tra mật khẩu user nhập có khớp với database không
        if ($request->password === $session->password) {
            // THÀNH CÔNG: Lưu cờ vào session để lần sau không hỏi lại
            session(['exam_access_' . $sessionId => true]);
            
            // Chuyển hướng lại trang làm bài (Lúc này logic ở takeExam sẽ cho qua)
            return redirect()->route('exam.take', $sessionId);
        }

        // THẤT BẠI: Quay lại báo lỗi
        return redirect()->back()->with('error', 'Mật khẩu kỳ thi không đúng!');
    }

    // 3. XỬ LÝ NỘP BÀI
    // Route: /exam/submit/{sessionId}
    public function submitExam(Request $request, $sessionId)
    {
        // TRƯỜNG HỢP 1: LUYỆN TẬP (Session ID = 0)
        if ($sessionId == 0) {
            $examId = $request->exam_id_hidden;
        } 
        // TRƯỜNG HỢP 2: THI CHÍNH THỨC
        else {
            $session = ExamSession::findOrFail($sessionId);
            $examId = $session->exam_id;

            // Kiểm tra thời gian nộp bài (cho phép trễ 2 phút)
            if (now() > $session->end_at->addMinutes(2)) {
                return redirect()->route('student.dashboard')->with('error', 'Quá giờ nộp bài!');
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

    // 4. XEM KẾT QUẢ
    // Route: /exam/result/{attemptId}
    public function showResult($attemptId)
    {
        $attemptDetail = ExamAttempt::with([
            'exam',
            'attemptAnswers.question.topic',
            'attemptAnswers.question.answers',
            'attemptAnswers.question.parent',
            'attemptAnswers.selectedAnswer'
        ])
        ->where('id', $attemptId)
        ->where('user_id', Auth::id())
        ->firstOrFail();

        $exam = $attemptDetail->exam;
        $score = $attemptDetail->total_score;
        $suggestions = $this->examService->getReviewSuggestions($attemptDetail->id);

        // Lấy dữ liệu vẽ biểu đồ
        $historyAttempts = ExamAttempt::where('exam_id', $attemptDetail->exam_id)
            ->where('user_id', Auth::id())
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'asc')
            ->get();

        $chartData = $historyAttempts->map(function ($item) use ($attemptDetail) {
            return [
                'date' => \Carbon\Carbon::parse($item->submitted_at)->format('d/m H:i'),
                'score' => $item->total_score,
                'is_current' => $item->id == $attemptDetail->id 
            ];
        })->values();

        return view('exam.result', compact('attemptDetail', 'exam', 'score', 'suggestions', 'chartData'));
    }

    // 5. BẮT ĐẦU LUYỆN TẬP
    public function startPractice($examId)
    {
        $exam = Exam::with(['questions' => function($q) {
            $q->whereNull('parent_id')->with(['answers', 'children.answers']);
        }])->findOrFail($examId);

        $session = new ExamSession();
        $session->id = 0; 
        $session->title = "Luyện tập: " . $exam->title;
        $session->exam_id = $exam->id;
        
        $session->start_at = now();
        $session->end_at = now()->addMinutes($exam->duration);

        return view('exam.take', compact('exam', 'session'));
    }

    // 6. LỊCH SỬ THI
    public function history()
    {
        $attempts = ExamAttempt::with(['exam', 'examSession'])
            ->where('user_id', Auth::id())
            ->orderBy('submitted_at', 'desc')
            ->get();

        $examAttempts = $attempts->filter(function ($item) {
            return !empty($item->exam_session_id) && $item->exam_session_id != 0;
        });

        $practiceAttempts = $attempts->filter(function ($item) {
            return empty($item->exam_session_id) || $item->exam_session_id == 0;
        });

        return view('history', compact('examAttempts', 'practiceAttempts'));
    }
}