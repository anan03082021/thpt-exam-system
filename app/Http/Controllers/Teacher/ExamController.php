<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Thêm Auth
use App\Models\Question;
use App\Models\Topic;
use App\Models\Competency;
use App\Models\CognitiveLevel;
use App\Models\Exam;
use App\Models\ExamAttempt; // [QUAN TRỌNG] Thêm dòng này để gọi được bảng kết quả thi

class ExamController extends Controller
{
    public function index()
    {
        // Lấy danh sách đề thi của giáo viên hiện tại
        $user = Auth::user();
        
        // Nếu muốn chỉ hiện đề của mình tạo:
        /*$exams = Exam::where('created_by', $user->id) 
                     ->latest()
                     ->paginate(10);*/
        $exams = Exam::latest()->paginate(10);
                     
        // Nếu hệ thống cho phép xem tất cả thì dùng: $exams = Exam::latest()->paginate(10);

        return view('teacher.exams.index', compact('exams'));
    }

    public function create(Request $request)
    {
        // 1. Khởi tạo Query
        $query = Question::query();

        // Chỉ lấy câu hỏi cha
        $query->whereNull('parent_id');

        // 2. Áp dụng các bộ lọc
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
        if ($request->filled('cognitive_level_id')) {
            $query->where('cognitive_level_id', $request->cognitive_level_id);
        }

        // 3. Lấy dữ liệu phân trang
        $questions = $query->with(['topic', 'cognitiveLevel', 'competency'])
                           ->latest()
                           ->paginate(20)
                           ->withQueryString();

        // 4. Lấy dữ liệu cho Dropdown
        $topics = Topic::all();
        $competencies = Competency::all();
        $levels = CognitiveLevel::all();

        return view('teacher.exams.create', compact('questions', 'topics', 'competencies', 'levels'));
    }

public function store(Request $request)
{
    // 1. Validate Data
    $request->validate([
        'title' => 'required',
        'duration' => 'required|integer',
        'question_ids' => 'required', // String like "1,5,9"
    ]);

    // 2. Handle Public/Private Status
    $isPublic = $request->has('is_public') ? true : false;

    // 3. Create Exam Record (Initial creation)
    $exam = Exam::create([
        'title' => $request->title,
        'duration' => $request->duration,
        'creator_id' => Auth::id(), 
        'is_public' => $isPublic,   
        'total_questions' => 0, // Initialize with 0
    ]);

    // 4. Handle Questions and Ordering
    $questionIds = explode(',', $request->question_ids);
    
    $pivotData = [];
    foreach ($questionIds as $index => $id) {
        // Prepare data for pivot table
        // Key is question ID, Value is array of extra columns
        $pivotData[$id] = ['order' => $index + 1];
    }

    // 5. Attach questions to the pivot table
    $exam->questions()->attach($pivotData);

    // 6. [CRITICAL FIX] Update total_questions count
    // This step was missing, causing the "0 questions" issue
    $exam->update([
        'total_questions' => count($questionIds)
    ]);

    return redirect()->route('teacher.exams.index')->with('success', 'Exam created successfully!');
}

    /**
     * [CẬP NHẬT] Hàm hiển thị kết quả thi chi tiết
     */
    public function results($id)
    {
        // 1. Lấy thông tin đề thi
        $exam = Exam::findOrFail($id);

        // 2. Lấy danh sách bài làm (attempts) của đề thi này
        // Kèm theo thông tin user để hiển thị tên học sinh
        $attempts = ExamAttempt::with('user')
            ->where('exam_id', $id)
            ->orderBy('total_score', 'desc') // Sắp xếp điểm cao nhất lên đầu
            ->get();

        // 3. Trả về View kết quả (file results.blade.php chúng ta đã làm đẹp)
        return view('teacher.exams.results', compact('exam', 'attempts'));
    }

    // --- [MỚI] Hàm hiển thị Form chỉnh sửa ---
    public function edit(Request $request, $id)
    {
        // 1. Lấy thông tin đề thi & câu hỏi đã chọn
        $exam = Exam::with('questions')->findOrFail($id);

        // Lấy danh sách ID câu hỏi đang có trong đề để truyền xuống View (cho JS xử lý)
        // pluck('id') lấy mảng [1, 5, 9...]
        $currentQuestionIds = $exam->questions->pluck('id')->toArray();

        // 2. Logic Lọc câu hỏi (Tương tự hàm create - Copy lại)
        $query = Question::query()->whereNull('parent_id');

        if ($request->filled('grade')) $query->where('grade', $request->grade);
        if ($request->filled('topic_id')) $query->where('topic_id', $request->topic_id);
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('competency_id')) $query->where('competency_id', $request->competency_id);
        if ($request->filled('cognitive_level_id')) $query->where('cognitive_level_id', $request->cognitive_level_id);

        // 3. Lấy dữ liệu phân trang
        $questions = $query->with(['topic', 'cognitiveLevel', 'competency'])
                           ->latest()
                           ->paginate(20)
                           ->withQueryString();

        // 4. Dữ liệu bổ trợ (Dropdown)
        $topics = Topic::all();
        $competencies = Competency::all();
        $levels = CognitiveLevel::all();

        return view('teacher.exams.edit', compact('exam', 'questions', 'topics', 'competencies', 'levels', 'currentQuestionIds'));
    }

    // --- [MỚI] Hàm xử lý Lưu cập nhật ---
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'duration' => 'required|integer',
            'question_ids' => 'required',
        ]);

        $exam = Exam::findOrFail($id);
        
        // 1. Cập nhật thông tin cơ bản
        $isPublic = $request->has('is_public') ? true : false;
        
        $exam->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'is_public' => $isPublic,
            // Không update creator_id
        ]);

        // 2. Xử lý danh sách ID câu hỏi
        $questionIds = explode(',', $request->question_ids);
        $pivotData = [];
        foreach ($questionIds as $index => $qId) {
            $pivotData[$qId] = ['order' => $index + 1];
        }

        // 3. [QUAN TRỌNG] Dùng sync() thay vì attach()
        // sync() sẽ: Xóa câu cũ không còn trong danh sách, Thêm câu mới, Cập nhật thứ tự câu cũ
        $exam->questions()->sync($pivotData);

        // 4. Cập nhật lại số lượng câu hỏi
        $exam->update(['total_questions' => count($questionIds)]);

        return redirect()->route('teacher.exams.index')->with('success', 'Cập nhật đề thi thành công!');
    }
    // Trong file Teacher\ExamController.php

public function destroy($id)
{
    $exam = Exam::where('creator_id', Auth::id())->findOrFail($id);
    
    // Xóa dữ liệu trong bảng trung gian trước (nếu chưa set cascade trong database)
    $exam->questions()->detach();
    
    // Xóa đề thi
    $exam->delete();

    return redirect()->back()->with('success', 'Đã xóa đề thi thành công.');
}
}