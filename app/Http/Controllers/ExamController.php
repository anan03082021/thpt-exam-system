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
use App\Models\Question;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    // =========================================================================
    // 1. VÀO LÀM BÀI (CHECK CA THI & MẬT KHẨU)
    // =========================================================================
    public function takeExam($sessionId)
    {
        // A. Lấy thông tin Ca thi & Câu hỏi
$session = ExamSession::with(['exam.questions' => function($q) {
        $q->whereNull('parent_id')
          ->with(['answers', 'children.answers'])
          ->withPivot('order') // Lấy cột order từ bảng trung gian
          ->orderBy('exam_questions.order', 'asc'); // Sắp xếp đúng thứ tự đã soạn
    }])->findOrFail($sessionId);

        // B. Validate: Kiểm tra thời gian
        $now = now();
        // Nếu là luyện tập (id=0) thì bỏ qua check giờ
        if ($sessionId != 0) {
            if ($now < $session->start_at) {
                return redirect()->route('dashboard')->with('error', 'Chưa đến giờ làm bài! Hãy quay lại lúc ' . $session->start_at->format('H:i d/m/Y'));
            }
            if ($now > $session->end_at) {
                return redirect()->route('dashboard')->with('error', 'Đã hết giờ làm bài!');
            }
        }

        // --- LOGIC KIỂM TRA QUYỀN ---
        $userEmail = Auth::user()->email;
        $isWhitelisted = $session->students()->where('student_email', $userEmail)->exists();
        $sessionKey = 'exam_access_' . $sessionId;
        $hasAccessByPassword = session()->has($sessionKey);

        // Cho phép nếu có trong danh sách, hoặc đã nhập mật khẩu, hoặc là bài luyện tập (id=0)
if ($isWhitelisted || $hasAccessByPassword || $sessionId == 0) {
        $exam = $session->exam;
        $allQuestions = $exam->questions;

        // Phân loại giữ nguyên logic của bạn
        $chungQuestions = $allQuestions->filter(function($q) {
            $val = strtolower(trim($q->orientation ?? '')); 
            return $val === 'chung' || $val === '';
        })->values(); // Thêm values() để reset index mảng

        $csQuestions = $allQuestions->filter(function($q) {
            return strtolower(trim($q->orientation ?? '')) === 'cs';
        })->values();

        $ictQuestions = $allQuestions->filter(function($q) {
            return strtolower(trim($q->orientation ?? '')) === 'ict';
        })->values();

        // Lấy elective từ session để truyền sang View tránh lỗi Undefined
        $savedElective = session('elective_choice_' . $sessionId, '');

        return view('exam.take', compact('exam', 'session', 'chungQuestions', 'csQuestions', 'ictQuestions', 'savedElective'));
    }

        // E. Form nhập mật khẩu
        if (!empty($session->password)) {
            return view('exam.password_check', compact('session'));
        }

        // F. Chặn
        return redirect()->route('dashboard')->with('error', 'Bạn không có quyền tham gia kỳ thi này.');
    }

    // =========================================================================
    // 2. XỬ LÝ NHẬP MẬT KHẨU
    // =========================================================================
    public function joinWithPassword(Request $request, $sessionId)
    {
        $session = ExamSession::findOrFail($sessionId);
        if ($request->password === $session->password) {
            session(['exam_access_' . $sessionId => true]);
            return redirect()->route('exam.take', $sessionId);
        }
        return redirect()->back()->with('error', 'Mật khẩu kỳ thi không đúng!');
    }

    // =========================================================================
    // 3. XỬ LÝ NỘP BÀI (LOGIC QUAN TRỌNG NHẤT - ĐÃ UPDATE)
    // =========================================================================
    public function submitExam(Request $request, $sessionId)
    {
        $isPractice = ($sessionId == 0); 

        // 1. Lấy lựa chọn của học sinh và chuẩn hóa
        $rawElective = $request->input('selected_elective');
        $selectedElective = strtolower(trim($rawElective ?? ''));

        // Validate: Nếu là thi chính thức và có phần tự chọn, bắt buộc phải chọn
        // Kiểm tra xem đề có phần tự chọn không (dựa vào request gửi lên có câu hỏi phần riêng không)
        // Tuy nhiên đơn giản nhất là check input selected_elective
        if (!$isPractice && !in_array($selectedElective, ['cs', 'ict'])) {
            // Có thể đề chỉ có phần chung, lúc đó selected_elective sẽ rỗng. 
            // Nếu đề có phân ban mà selected_elective rỗng -> Chặn.
            // Để an toàn, ta tạm bỏ qua validate cứng ở đây nếu đề thuần Chung, 
            // nhưng Frontend đã ép buộc chọn rồi.
        }

        // 2. Xử lý thông tin kỳ thi
        if ($isPractice) {
            $examId = $request->exam_id_hidden;
        } else {
            $session = ExamSession::findOrFail($sessionId);
            $examId = $session->exam_id;
            // Cho phép trễ 2 phút do độ trễ mạng
            if (now() > $session->end_at->addMinutes(2)) {
                return redirect()->route('dashboard')->with('error', 'Quá giờ nộp bài!');
            }
        }

        // 3. Tạo lượt thi (Attempt)
        $attempt = ExamAttempt::create([
            'user_id' => Auth::id(),
            'exam_id' => $examId,
            'exam_session_id' => $sessionId, 
            'started_at' => now(), // Lý tưởng là lấy từ session lúc start
            'submitted_at' => now(),
            'total_score' => 0,
            // Nếu bạn đã thêm cột này vào DB thì bỏ comment dòng dưới:
            // 'selected_elective' => $selectedElective 
        ]);

        // 4. Lưu câu trả lời (CÓ LỌC)
        if ($request->has('answers')) {
            foreach ($request->answers as $questionId => $answerId) {
                // Load question để kiểm tra orientation
                $answer = Answer::with('question')->find($answerId);
                
                if ($answer) {
                    $question = $answer->question;
                    // Chuẩn hóa orientation của câu hỏi
                    $qOri = strtolower(trim($question->orientation ?? ''));
                    if ($qOri === '') $qOri = 'chung';

                    // --- LOGIC QUYẾT ĐỊNH CÓ LƯU HAY KHÔNG ---
                    $shouldSave = false;

                    // Luôn lưu phần Chung
                    if ($qOri === 'chung') {
                        $shouldSave = true;
                    }
                    // Nếu là bài Luyện tập -> Lưu hết (để học sinh xem lại)
                    elseif ($isPractice) {
                        $shouldSave = true; 
                    }
                    // Nếu là Thi thật -> CHỈ LƯU phần khớp với lựa chọn
                    elseif ($qOri === $selectedElective) {
                        $shouldSave = true;
                    }

                    // Nếu không thỏa mãn -> Bỏ qua (Continue)
                    if (!$shouldSave) {
                        continue; 
                    }

                    // Lưu vào DB
                    AttemptAnswer::create([
                        'attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                        'selected_answer_id' => $answerId,
                        'is_correct' => $answer->is_correct
                    ]);
                }
            }
        }

        // 5. Tính điểm
        $this->examService->calculateScore($attempt->id);

        // Xóa session lựa chọn để lần sau thi lại (nếu có) được chọn lại
        session()->forget('elective_choice_' . $sessionId);

        if ($isPractice) {
            session()->forget('practice_end_time_' . $examId);
        } else {
            return redirect()->route('student.exam.result.official', $attempt->id);
        }
    }

// =========================================================================
    // 4. BẮT ĐẦU LUYỆN TẬP
    // =========================================================================
    public function startPractice($examId)
    {
        $exam = Exam::with(['questions' => function($q) {
            $q->whereNull('parent_id')
              ->with(['answers', 'children.answers'])
              ->withPivot('order')
              ->orderBy('exam_questions.order', 'asc');
        }])->findOrFail($examId);

        $session = new ExamSession();
        $session->id = 0; 
        $session->title = "Luyện tập: " . $exam->title;
        $session->exam_id = $exam->id;
        $session->start_at = now();
        
        // --- SỬA LẠI ĐỂ KHÔNG RESET THỜI GIAN KHI F5 ---
        $sessionKey = 'practice_end_time_' . $exam->id;
        if (session()->has($sessionKey)) {
            // Nếu đã có thời gian kết thúc lưu trong session thì lấy ra dùng
            $session->end_at = session($sessionKey);
            
            // Nếu F5 mà thời gian lưu cũ đã quá hạn (hết giờ), ta tạo mới lại 45p
            if (now() > $session->end_at) {
                $session->end_at = now()->addMinutes($exam->duration);
                session([$sessionKey => $session->end_at]);
            }
        } else {
            // Nếu là lần đầu tiên mở đề, lưu mốc thời gian kết thúc vào session
            $session->end_at = now()->addMinutes($exam->duration);
            session([$sessionKey => $session->end_at]);
        }
        // ------------------------------------------------

        $allQuestions = $exam->questions;
        
        $chungQuestions = $allQuestions->filter(fn($q) => in_array(strtolower(trim($q->orientation ?? '')), ['chung', '']))->values();
        $csQuestions = $allQuestions->filter(fn($q) => strtolower(trim($q->orientation ?? '')) === 'cs')->values();
        $ictQuestions = $allQuestions->filter(fn($q) => strtolower(trim($q->orientation ?? '')) === 'ict')->values();

        $savedElective = session('elective_choice_0', '');

        return view('exam.take', compact('exam', 'session', 'chungQuestions', 'csQuestions', 'ictQuestions', 'savedElective'));
    }


// =========================================================================
    // 5. LỊCH SỬ THI (CÁCH: LỌC THỦ CÔNG - ĐẢM BẢO 100% SẠCH)
    // =========================================================================
    public function history()
    {
        // 1. Lấy tất cả bài làm của user về trước
        $allAttempts = ExamAttempt::with(['exam', 'examSession'])
            ->where('user_id', Auth::id())
            ->orderBy('submitted_at', 'desc')
            ->get();

        // 2. Lọc bỏ dữ liệu rác bằng PHP (Vòng lặp kiểm tra từng cái)
        $validAttempts = $allAttempts->filter(function ($attempt) {
            // Kiểm tra 1: Đề thi gốc có còn tồn tại không?
            if (!$attempt->exam) {
                return false; // Không còn -> Loại bỏ
            }

            // Kiểm tra 2: Nếu là Thi Chính Thức (có session_id), thì Kỳ thi có còn không?
            // (Lưu ý: session_id = 0 là luyện tập, không cần kiểm tra session)
            if (!empty($attempt->exam_session_id) && $attempt->exam_session_id != 0) {
                if (!$attempt->examSession) {
                    return false; // Kỳ thi bị xóa rồi -> Loại bỏ
                }
            }

            return true; // Dữ liệu hợp lệ -> Giữ lại
        });

        // 3. Phân loại từ danh sách đã làm sạch ($validAttempts)
        // Dùng values() để reset lại key mảng cho View dễ xử lý
        $examAttempts = $validAttempts->filter(function ($item) {
            return !empty($item->exam_session_id) && $item->exam_session_id != 0;
        })->values();

        $practiceAttempts = $validAttempts->filter(function ($item) {
            return empty($item->exam_session_id) || $item->exam_session_id == 0;
        })->values();

        return view('history', compact('examAttempts', 'practiceAttempts'));
    }

    // =========================================================================
    // 6. HIỂN THỊ KẾT QUẢ KỲ THI CHÍNH THỨC
    // =========================================================================
    public function showOfficialResult($id)
    {
        $userId = Auth::id();

        $attempt = ExamAttempt::with([
            'examSession',
            'attemptAnswers.selectedAnswer',
            'attemptAnswers.question.answers', 
            'attemptAnswers.question.topic',
            // Load các quan hệ khác...
        ])
        ->where('user_id', $userId)
        ->findOrFail($id);

        // Nhóm câu trả lời theo câu hỏi cha (cho dạng chùm)
        $groupedQuestions = $attempt->attemptAnswers->groupBy(function ($ans) {
            return $ans->question->parent_id ?? $ans->question->id;
        });

        // Thống kê điểm số so với lớp
        $sessionId = $attempt->exam_session_id;
        $averageScore = 0; $maxScore = 0; $beatCount = 0;

        if ($sessionId) {
            $averageScore = ExamAttempt::where('exam_session_id', $sessionId)->avg('total_score');
            $maxScore = ExamAttempt::where('exam_session_id', $sessionId)->max('total_score');
            $beatCount = ExamAttempt::where('exam_session_id', $sessionId)
                ->where('total_score', '<', $attempt->total_score)
                ->count();
        }

        // [MỚI] Xác định người dùng đã chọn môn nào dựa trên các câu đã trả lời
        // Để hiển thị badge hoặc lọc thêm nếu cần
        $detectedElective = null;
        foreach($attempt->attemptAnswers as $ans) {
            $ori = strtolower(trim($ans->question->orientation ?? ''));
            if ($ori === 'cs') { $detectedElective = 'cs'; break; }
            if ($ori === 'ict') { $detectedElective = 'ict'; break; }
        }

        return view('exam.result_official', compact(
            'attempt', 'groupedQuestions', 'averageScore', 'maxScore', 'beatCount', 'detectedElective'
        ));
    }

    // =========================================================================
    // 7. HIỂN THỊ KẾT QUẢ LUYỆN TẬP
    // =========================================================================
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

        // Biểu đồ lịch sử
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

    // =========================================================================
    // 8. API LƯU TRẠNG THÁI CHỌN MÔN (CHO JS GỌI)
    // =========================================================================
    public function saveElective(Request $request, $sessionId)
{
    $request->validate([
        'elective' => 'required|in:cs,ict'
    ]);

    // 1. Lưu vào Session
    session(['elective_choice_' . $sessionId => $request->elective]);

    // 2. Lấy danh sách câu hỏi của môn đã chọn
    $session = ExamSession::with(['exam.questions' => function($q) {
        $q->whereNull('parent_id')->with(['answers', 'children.answers']);
    }])->findOrFail($sessionId);

    $questions = $session->exam->questions->filter(function($q) use ($request) {
        return strtolower(trim($q->orientation ?? '')) === $request->elective;
    })->values();

    // 3. Trả về JSON để Frontend render thêm câu hỏi
    return response()->json([
        'success' => true,
        'questions' => $questions
    ]);
}
}