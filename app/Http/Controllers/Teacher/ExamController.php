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
        $exams = Exam::latest()->paginate(10);
        return view('teacher.exams.index', compact('exams'));
    }

    public function create(Request $request)
    {
        $query = Question::query()->whereNull('parent_id');

        if ($request->filled('grade')) $query->where('grade', $request->grade);
        if ($request->filled('topic_id')) $query->where('topic_id', $request->topic_id);
        if ($request->filled('orientation')) $query->where('orientation', $request->orientation);
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('competency_id')) $query->where('competency_id', $request->competency_id);
        if ($request->filled('cognitive_level_id')) $query->where('cognitive_level_id', $request->cognitive_level_id);
        if ($request->filled('source')) $query->where('source', $request->source);

        $questions = $query->with(['topic', 'cognitiveLevel', 'competency', 'answers', 'children.answers'])
            ->latest()->paginate(20)->withQueryString();

        $topics = Topic::all();
        $competencies = Competency::orderBy('code', 'asc')->get();
        $levels = CognitiveLevel::all();

        return view('teacher.exams.create', compact('questions', 'topics', 'competencies', 'levels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:200',
            'duration' => 'required|integer|min:5',
            'question_ids' => 'nullable|string', 
            'new_questions' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $isPublic = $request->has('is_public') ? true : false;
            $exam = Exam::create([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'creator_id' => Auth::id(),
                'is_public' => $isPublic,
                'total_questions' => 0, 
            ]);

            $finalQuestionIds = [];

            if (!empty($request->question_ids)) {
                $bankIds = explode(',', $request->question_ids);
                $finalQuestionIds = array_merge($finalQuestionIds, $bankIds);
            }

            if (!empty($request->new_questions)) {
                foreach ($request->new_questions as $qData) {
                    $newQuestion = Question::create([
                        'content' => $qData['content'],
                        'type' => 'single_choice',
                        'grade' => $qData['grade'],
                        'topic_id' => $qData['topic_id'],
                        'orientation' => $qData['orientation'],
                        'cognitive_level_id' => null, 
                        'level' => $qData['level'] ?? 'medium',
                        'user_id' => Auth::id(),
                        'source' => 'user',
                    ]);

                    foreach ($qData['options'] as $idx => $optContent) {
                        if(!empty($optContent)) {
                            Answer::create([
                                'question_id' => $newQuestion->id,
                                'content' => $optContent,
                                'is_correct' => ($idx == $qData['correct_index']),
                            ]);
                        }
                    }
                    $finalQuestionIds[] = $newQuestion->id;
                }
            }

            $pivotData = [];
            foreach ($finalQuestionIds as $index => $id) {
                $pivotData[$id] = ['order' => $index + 1];
            }
            $exam->questions()->sync($pivotData);
            $exam->update(['total_questions' => count($finalQuestionIds)]);

            DB::commit();
            return redirect()->route('teacher.exams.index')->with('success', 'Tạo đề thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Exam Store Error: " . $e->getMessage());
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage())->withInput();
        }
    }

    public function results($id)
    {
        $exam = Exam::findOrFail($id);
        $attempts = ExamAttempt::with('user')->where('exam_id', $id)->orderBy('total_score', 'desc')->get();
        return view('teacher.exams.results', compact('exam', 'attempts'));
    }

public function edit($id)
{
    // 1. Load dữ liệu đề thi kèm các mối quan hệ (Giữ nguyên logic sắp xếp order)
    $exam = Exam::with([
        'questions' => function($query) {
            $query->withPivot('order')->orderBy('exam_questions.order', 'asc');
        },
        'questions.answers',
        'questions.children.answers',
        'questions.cognitiveLevel',          
        'questions.children.cognitiveLevel',
        'questions.topic' // Load thêm topic để lấy topic_name
    ])->findOrFail($id);

    // Map mức độ từ Database sang mã giao diện (Helper mapping)
    $dbToViewMap = [
        'nhận biết' => 'easy',
        'thông hiểu' => 'medium',
        'vận dụng' => 'hard',
        'vận dụng cao' => 'very_hard',
        'easy' => 'easy', 'medium' => 'medium', 'hard' => 'hard', 'very_hard' => 'very_hard'
    ];

    $transformLevel = function($q) use ($dbToViewMap) {
        $lvName = strtolower($q->cognitiveLevel->name ?? 'easy'); 
        return $dbToViewMap[$lvName] ?? 'easy';
    };

    // 2. Map dữ liệu câu hỏi hiện có trong đề
    $selectedQuestions = $exam->questions->map(function ($q) use ($transformLevel) {
        $answers = [];
        $parentLevel = $transformLevel($q);

        if ($q->type == 'single_choice') {
            $answers = $q->answers->map(function($a) {
                return [
                    'id' => $a->id,
                    'content' => $a->content,
                    'is_correct' => (bool)$a->is_correct
                ];
            });
        } elseif ($q->type == 'true_false_group') {
            $answers = $q->children->map(function($c) use ($transformLevel) {
                return [
                    'id' => $c->id,
                    'content' => $c->content,
                    'level' => $transformLevel($c), 
                    'is_correct' => $c->answers->where('is_correct', true)->first()->content ?? ''
                ];
            });
        }

        return [
            'id' => (string)$q->id,
            'type' => strtolower($q->orientation ?? 'chung'),
            'content' => $q->content,
            'level' => $parentLevel,
            'q_type' => $q->type,
            'topic_id' => $q->topic_id,
            'topic_name' => $q->topic->name ?? 'Chưa phân loại',
            'answers' => $answers
        ];
    });

    // 3. Lấy dữ liệu danh mục cho 6 bộ lọc
    $topics = Topic::all();
    $competencies = Competency::all(); // Năng lực
    $levels = CognitiveLevel::all();   // Mức độ (Dạng câu hỏi)
    // Lớp, Nguồn, Định hướng thường là giá trị cố định, xử lý ở View hoặc mảng đơn giản

    // 4. Truy vấn Ngân hàng câu hỏi với đầy đủ 6 yếu tố lọc
    $query = Question::whereNull('parent_id')
        ->with(['answers', 'topic', 'children.cognitiveLevel', 'cognitiveLevel', 'competency']);

    // Áp dụng lọc theo Request
    if (request()->filled('grade')) {
        $query->where('grade', request('grade'));
    }
    if (request()->filled('topic_id')) {
        $query->where('topic_id', request('topic_id'));
    }
    if (request()->filled('competency_id')) {
        $query->where('competency_id', request('competency_id'));
    }
    if (request()->filled('level')) { // Lọc theo ID mức độ (cognitive_level_id)
        $query->where('cognitive_level_id', request('level'));
    }
    if (request()->filled('orientation')) {
        $query->where('orientation', request('orientation'));
    }
    if (request()->filled('source')) {
        $query->where('source', request('source'));
    }

    // Lấy tất cả hoặc phân trang (tăng lên 20 để dễ quan sát)
    $questions = $query->latest()->paginate(20)->withQueryString();

    return view('teacher.exams.edit', compact(
        'exam', 
        'questions', 
        'selectedQuestions', 
        'topics', 
        'competencies', 
        'levels'
    ));
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'question_ids' => 'required',
            'edited_contents' => 'nullable|array',
            'edited_answers' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();
            $exam = Exam::findOrFail($id);
            
            $exam->update([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'is_public' => $request->has('is_public'),
            ]);

            // 1. Chuẩn bị Map Level (View -> ID Database)
            $allLevels = \App\Models\CognitiveLevel::all();
            
            $getLevelId = function($text) use ($allLevels) {
                $map = [
                    'easy' => ['easy', 'nhận biết', 'nb'],
                    'medium' => ['medium', 'thông hiểu', 'th'],
                    'hard' => ['hard', 'vận dụng', 'vd'],
                    'very_hard' => ['very_hard', 'vận dụng cao', 'vdc'],
                ];
                
                foreach ($allLevels as $lv) {
                    $dbName = strtolower($lv->name);
                    if (in_array($dbName, $map[$text] ?? [])) return $lv->id;
                    if ($dbName == $text) return $lv->id;
                }
                return null;
            };

            // 2. Cập nhật câu hỏi (Content)
            if ($request->filled('edited_contents')) {
                foreach ($request->edited_contents as $qId => $content) {
                    if (!empty($content)) {
                        Question::where('id', $qId)->update(['content' => $content]);
                    }
                }
            }

            // 3. Cập nhật Đáp án & Level
            if ($request->filled('edited_answers')) {
                foreach ($request->edited_answers as $qId => $answersJson) {
                    $answersData = json_decode($answersJson, true);
                    if (!$answersData) continue;

                    $mainQuestion = Question::find($qId);
                    if (!$mainQuestion) continue;

                    if ($mainQuestion->type === 'true_false_group') {
                        $children = Question::where('parent_id', $qId)->get();
                        foreach ($answersData as $index => $data) {
                            if (isset($children[$index])) {
                                // Tìm ID của level mới
                                $newLevelId = isset($data['level']) ? $getLevelId($data['level']) : null;
                                
                                $updateData = ['content' => $data['content']];
                                if ($newLevelId) {
                                    $updateData['cognitive_level_id'] = $newLevelId;
                                }

                                $children[$index]->update($updateData);
                                
                                $isCorrect = ($data['is_correct'] === 'Đúng' || $data['is_correct'] === true || $data['is_correct'] == 1);
                                Answer::where('question_id', $children[$index]->id)->update(['is_correct' => $isCorrect]);
                            }
                        }
                    } elseif ($mainQuestion->type === 'single_choice') {
                        $dbAnswers = Answer::where('question_id', $qId)->get();
                        foreach ($answersData as $index => $data) {
                            if (isset($dbAnswers[$index])) {
                                $dbAnswers[$index]->update([
                                    'content' => $data['content'],
                                    'is_correct' => filter_var($data['is_correct'], FILTER_VALIDATE_BOOLEAN)
                                ]);
                            }
                        }
                    }
                }
            }

            // 4. Sync Exam Questions
            $questionIds = explode(',', $request->question_ids);
            $pivotData = [];
            foreach ($questionIds as $index => $qId) {
                $pivotData[$qId] = ['order' => $index + 1];
            }
            $exam->questions()->sync($pivotData);
            $exam->update(['total_questions' => count($questionIds)]);

            DB::commit();
            return redirect()->route('teacher.exams.index')->with('success', 'Đã lưu thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Exam Update Error: " . $e->getMessage());
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $exam = Exam::where('creator_id', Auth::id())->findOrFail($id);
        $exam->delete();
        return redirect()->back()->with('success', 'Đề thi đã được ẩn khỏi danh sách.');
    }
}