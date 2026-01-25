<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Question;
use App\Models\Topic;
use App\Models\Competency;
use App\Models\CognitiveLevel;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Answer;

class ExamController extends Controller
{
    public function index()
    {
        // Hiển thị danh sách đề thi (giữ nguyên logic của bạn)
        $exams = Exam::latest()->paginate(10);
        return view('teacher.exams.index', compact('exams'));
    }

    public function create(Request $request)
    {
        // 1. Khởi tạo Query (Chỉ lấy câu hỏi cha, không lấy câu hỏi con)
        $query = Question::query()->whereNull('parent_id');

        // 2. Áp dụng các bộ lọc (Filters)
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

        // [MỚI] Bộ lọc theo Nguồn dữ liệu (thpt_2025 hoặc user)
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // 3. Lấy dữ liệu và phân trang
        // Eager load các quan hệ cần thiết để hiển thị (bao gồm cả đáp án để xem trước)
        $questions = $query->with([
            'topic', 
            'cognitiveLevel', 
            'competency', 
            'answers',           // Lấy đáp án cho câu trắc nghiệm
            'children.answers'   // Lấy câu con và đáp án cho câu chùm
        ])
        ->latest()
        ->paginate(20)
        ->withQueryString(); // Giữ lại các tham số lọc trên URL khi chuyển trang

        // 4. Lấy dữ liệu cho các Dropdown bộ lọc
        $topics = Topic::all();
        $competencies = Competency::all();
        $levels = CognitiveLevel::all();

        return view('teacher.exams.create', compact('questions', 'topics', 'competencies', 'levels'));
    }

    public function store(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:5',
            'question_ids' => 'nullable|string', 
            
            // Validate mảng câu hỏi mới (nếu có)
            'new_questions' => 'nullable|array',
            'new_questions.*.content' => 'required_with:new_questions|string',
            'new_questions.*.grade' => 'required_with:new_questions|integer',
            'new_questions.*.topic_id' => 'required_with:new_questions|exists:topics,id',
            'new_questions.*.level' => 'required_with:new_questions|string',
            'new_questions.*.orientation' => 'required_with:new_questions|in:chung,cs,ict', // Bắt buộc chọn định hướng
            
            'new_questions.*.options' => 'required_with:new_questions|array|min:2',
            'new_questions.*.correct_index' => 'required_with:new_questions|integer',
        ]);

        try {
            DB::beginTransaction();

            // 2. Tạo Đề thi
            $isPublic = $request->has('is_public') ? true : false;
            $exam = Exam::create([
                'title' => $request->title,
                'duration' => $request->duration,
                'creator_id' => Auth::id(),
                'is_public' => $isPublic,
                'total_questions' => 0, 
            ]);

            $finalQuestionIds = [];

            // 3. Xử lý câu hỏi từ NGÂN HÀNG (đã chọn qua checkbox)
            if (!empty($request->question_ids)) {
                $bankIds = explode(',', $request->question_ids);
                $finalQuestionIds = array_merge($finalQuestionIds, $bankIds);
            }

            // 4. Xử lý câu hỏi TẠO MỚI (Soạn thảo trực tiếp)
            if (!empty($request->new_questions)) {
                foreach ($request->new_questions as $qData) {
                    
                    // 4a. Tạo câu hỏi mới
                    $newQuestion = Question::create([
                        'content' => $qData['content'],
                        'type' => 'single_choice', // Mặc định trắc nghiệm (hoặc sửa logic nếu hỗ trợ nhiều loại)
                        'grade' => $qData['grade'],
                        'topic_id' => $qData['topic_id'],
                        'orientation' => $qData['orientation'],
                        'cognitive_level_id' => null, 
                        'level' => $qData['level'] ?? 'medium',
                        'user_id' => Auth::id(),
                        'source' => 'user', // Đánh dấu là câu hỏi do user tạo
                    ]);

                    // 4b. Tạo đáp án cho câu hỏi mới
                    foreach ($qData['options'] as $idx => $optContent) {
                        Answer::create([
                            'question_id' => $newQuestion->id,
                            'content' => $optContent,
                            'is_correct' => ($idx == $qData['correct_index']),
                        ]);
                    }

                    $finalQuestionIds[] = $newQuestion->id;
                }
            }

            // 5. Gắn câu hỏi vào đề thi (Sync Pivot Table)
            $pivotData = [];
            foreach ($finalQuestionIds as $index => $id) {
                $pivotData[$id] = ['order' => $index + 1];
            }
            $exam->questions()->sync($pivotData);
            
            // 6. Cập nhật tổng số câu hỏi
            $exam->update(['total_questions' => count($finalQuestionIds)]);

            // 7. Thống kê phân loại (Optional: để hiển thị thông báo chi tiết)
            $stats = Question::whereIn('id', $finalQuestionIds)
                ->select('orientation', DB::raw('count(*) as total'))
                ->groupBy('orientation')
                ->pluck('total', 'orientation')
                ->toArray();

            $stats = array_change_key_case($stats, CASE_LOWER);
            $countChung = ($stats['chung'] ?? 0) + ($stats[''] ?? 0); 
            $countCS = $stats['cs'] ?? 0;
            $countICT = $stats['ict'] ?? 0;

            DB::commit();

            $msg = "Tạo đề thành công! (Tổng: " . count($finalQuestionIds) . " câu). <br>Phân loại: <b>{$countChung} Chung</b> - <b>{$countCS} CS</b> - <b>{$countICT} ICT</b>";

            return redirect()->route('teacher.exams.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function results($id)
    {
        $exam = Exam::findOrFail($id);
        $attempts = ExamAttempt::with('user')
            ->where('exam_id', $id)
            ->orderBy('total_score', 'desc')
            ->get();

        return view('teacher.exams.results', compact('exam', 'attempts'));
    }

    public function edit(Request $request, $id)
    {
        $exam = Exam::with('questions')->findOrFail($id);
        $currentQuestionIds = $exam->questions->pluck('id')->toArray();

        $query = Question::query()->whereNull('parent_id');

        if ($request->filled('grade')) $query->where('grade', $request->grade);
        if ($request->filled('topic_id')) $query->where('topic_id', $request->topic_id);
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('competency_id')) $query->where('competency_id', $request->competency_id);
        if ($request->filled('cognitive_level_id')) $query->where('cognitive_level_id', $request->cognitive_level_id);
        
        // Thêm bộ lọc source cho trang edit nếu cần
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $questions = $query->with([
            'topic', 
            'cognitiveLevel', 
            'competency', 
            'answers', 
            'children.answers'
        ])
        ->latest()
        ->paginate(20)
        ->withQueryString();

        $topics = Topic::all();
        $competencies = Competency::all();
        $levels = CognitiveLevel::all();

        return view('teacher.exams.edit', compact('exam', 'questions', 'topics', 'competencies', 'levels', 'currentQuestionIds'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'duration' => 'required|integer',
            'question_ids' => 'required',
        ]);

        $exam = Exam::findOrFail($id);
        
        $isPublic = $request->has('is_public') ? true : false;
        
        $exam->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'is_public' => $isPublic,
        ]);

        $questionIds = explode(',', $request->question_ids);
        $pivotData = [];
        foreach ($questionIds as $index => $qId) {
            $pivotData[$qId] = ['order' => $index + 1];
        }

        $exam->questions()->sync($pivotData);
        $exam->update(['total_questions' => count($questionIds)]);

        return redirect()->route('teacher.exams.index')->with('success', 'Cập nhật đề thi thành công!');
    }

    public function destroy($id)
    {
        $exam = Exam::where('creator_id', Auth::id())->findOrFail($id);
        $exam->delete();
        return redirect()->back()->with('success', 'Đề thi đã được ẩn khỏi danh sách.');
    }
}