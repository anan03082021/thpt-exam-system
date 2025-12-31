<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function index()
    {
        // Thống kê sơ bộ
        $examCount = Exam::where('creator_id', Auth::id())->count();
        $questionCount = Question::count(); // Tạm thời đếm hết câu hỏi
        
        // Lấy danh sách đề thi do giáo viên này tạo
        $myExams = Exam::where('creator_id', Auth::id())->orderByDesc('created_at')->get();

        return view('teacher.dashboard', compact('examCount', 'questionCount', 'myExams'));
    }

    // 1. HIỂN THỊ FORM TẠO ĐỀ
    public function create()
    {
        // Lấy danh sách câu hỏi để giáo viên chọn
        // Chỉ lấy câu hỏi đơn (D1) hoặc câu hỏi cha của nhóm (D2)
        // Kèm theo chủ đề để dễ phân loại
        $questions = Question::with('topic')
            ->whereIn('type', ['single_choice', 'true_false_group'])
            ->get();

        return view('teacher.exams.create', compact('questions'));
    }

    // 2. LƯU ĐỀ THI VÀO CSDL
    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'questions' => 'required|array|min:1', // Phải chọn ít nhất 1 câu
        ]);

        // Tạo đề thi mới
        $exam = Exam::create([
            'title' => $request->title,
            'creator_id' => Auth::id(),
            'duration' => $request->duration,
            'status' => 'published', // Tạm thời public luôn
            'total_questions' => count($request->questions)
        ]);

        // Lưu danh sách câu hỏi vào bảng trung gian (exam_questions)
        $order = 1;
        foreach ($request->questions as $questionId) {
            // Tìm câu hỏi để xác định điểm số
            $q = Question::find($questionId);
            
            // Logic điểm số theo cấu trúc mới:
            // Dạng 1 (Trắc nghiệm): 0.25 điểm
            // Dạng 2 (Đúng/Sai chùm): 1.0 điểm
            $score = ($q->type == 'single_choice') ? 0.25 : 1.0;

            // Dùng hàm attach của Eloquent để lưu vào bảng trung gian
            $exam->questions()->attach($questionId, [
                'order' => $order++,
                'score_weight' => $score
            ]);
        }

        return redirect()->route('teacher.dashboard')->with('success', 'Đã tạo đề thi thành công!');
    }

    public function examResults($id)
    {
        // Lấy thông tin đề thi
        $exam = Exam::findOrFail($id);

        // Kiểm tra xem đề này có phải do giáo viên này tạo không (Bảo mật)
        if ($exam->creator_id !== Auth::id()) {
            return redirect()->route('teacher.dashboard')->with('error', 'Bạn không có quyền xem đề này.');
        }

        // Lấy danh sách các lượt thi (Attempts) của đề này
        // Kèm thông tin User để hiện tên học sinh
        $attempts = ExamAttempt::with('user')
            ->where('exam_id', $id)
            ->orderByDesc('total_score') // Sắp xếp điểm từ cao xuống thấp
            ->get();

        return view('teacher.exams.results', compact('exam', 'attempts'));
    }
}