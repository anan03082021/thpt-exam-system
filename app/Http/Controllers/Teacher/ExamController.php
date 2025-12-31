<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Topic;
use App\Models\Competency;
use App\Models\CognitiveLevel;
use App\Models\Exam;

class ExamController extends Controller
{
    public function index()
    {
        // Lấy danh sách đề thi (có thể lọc theo người tạo nếu cần)
        // Giả sử lấy tất cả đề thi mới nhất
        $exams = Exam::latest()->paginate(10);

        return view('teacher.exams.index', compact('exams'));
    }

    public function create(Request $request)
    {
        // 1. Khởi tạo Query
        $query = Question::query();

        // Chỉ lấy câu hỏi cha (không lấy câu hỏi con của dạng chùm để tránh trùng lặp)
        $query->whereNull('parent_id');

        // 2. Áp dụng các bộ lọc nếu có dữ liệu gửi lên
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }

        if ($request->filled('orientation')) {
            $query->where('orientation', $request->orientation);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('competency_id')) {
            $query->where('competency_id', $request->competency_id);
        }

        // Lưu ý: Dạng chùm (Dạng 2) có level = null, nên nếu lọc level sẽ mất dạng chùm
        if ($request->filled('cognitive_level_id')) {
            $query->where('cognitive_level_id', $request->cognitive_level_id);
        }

        // 3. Lấy dữ liệu phân trang (20 câu mỗi trang) & Giữ lại tham số lọc trên URL
        $questions = $query->with(['topic', 'cognitiveLevel', 'competency'])
                           ->latest()
                           ->paginate(20)
                           ->withQueryString();

        // 4. Lấy dữ liệu cho các Dropdown
        $topics = Topic::all();
        $competencies = Competency::all();
        $levels = CognitiveLevel::all();

        return view('teacher.exams.create', compact('questions', 'topics', 'competencies', 'levels'));
    }

public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'duration' => 'required|integer',
            'question_ids' => 'required', // Đây là chuỗi "1,5,9"
        ]);

        // Tách chuỗi thành mảng
        $questionIds = explode(',', $request->question_ids);

        // Tạo đề thi
        $exam = \App\Models\Exam::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'password' => $request->password,
            // Thêm user_id nếu cần (Auth::id())
        ]);

        // Lưu câu hỏi vào bảng trung gian (pivot)
        // Attach nhận vào một mảng ID
        $exam->questions()->attach($questionIds);

        return redirect()->route('teacher.exams.index')->with('success', 'Tạo đề thi thành công!');
    }

    public function results($id)
    {
        // Tạm thời hiển thị thông báo để test. 
        // Sau này bạn có thể query database để lấy danh sách điểm thi.
        return "Chức năng xem kết quả chi tiết cho Đề thi #$id đang được xây dựng.";
        
        // Gợi ý code tương lai:
        // $exam = Exam::with('attempts.user')->findOrFail($id);
        // return view('teacher.exams.results', compact('exam'));
    }
}