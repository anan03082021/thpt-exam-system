<?php

namespace App\Http\Controllers\Teacher; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Competency;
use App\Models\CognitiveLevel;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    // 1. Hiển thị danh sách câu hỏi
    public function index()
    {
        $questions = Question::with(['topic', 'competency', 'cognitiveLevel'])
            ->whereIn('type', ['single_choice', 'true_false_group'])
            ->orderByDesc('id')
            ->paginate(10);
            
        // Dropdown cho Modal Thêm nhanh
        $topics = Topic::all();
        $competencies = Competency::all();
        $levels = CognitiveLevel::all();

        return view('teacher.questions.index', compact('questions', 'topics', 'competencies', 'levels'));
    }

    // 2. Hiển thị form tạo mới
    public function create()
    {
        // [MỚI] Load Topic kèm theo quan hệ để làm Dropdown phụ thuộc (nếu cần xử lý JS)
        $competencies = Competency::all();
        $levels = CognitiveLevel::all();
        
        return view('teacher.questions.create', compact('competencies', 'levels'));
    }

    // 3. Xử lý lưu câu hỏi (Full)
    public function store(Request $request)
    {
        // 1. Validate
        $rules = [
            'content' => 'required',
            'topic_id' => 'required',
            'grade' => 'required',
            'orientation' => 'required',
            'competency_id' => 'required',
            'type' => 'required',
            // [MỚI] Validate 2 trường mới (cho phép null)
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'core_content_id' => 'nullable|exists:core_contents,id',
        ];

        if ($request->type == 'single_choice') {
            $rules['cognitive_level_id'] = 'required';
        } else {
            $rules['sub_questions.*.content'] = 'required';
            $rules['sub_questions.*.cognitive_level_id'] = 'required';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $commonData = [
                'content' => $request->content,
                'topic_id' => $request->topic_id,
                'grade' => $request->grade,
                'orientation' => strtolower($request->orientation),
                'competency_id' => $request->competency_id,
                
                // [MỚI] Lưu dữ liệu Yêu cầu cần đạt & Nội dung trọng tâm
                'learning_objective_id' => $request->learning_objective_id,
                'core_content_id' => $request->core_content_id,
                
                // 'created_by' => Auth::id(),
            ];

            // --- TRƯỜNG HỢP 1: TRẮC NGHIỆM ---
            if ($request->type == 'single_choice') {
                $commonData['type'] = 'single_choice';
                $commonData['cognitive_level_id'] = $request->cognitive_level_id;
                
                $question = Question::create($commonData);

                foreach ($request->answers as $key => $ansContent) {
                    Answer::create([
                        'question_id' => $question->id,
                        'content' => $ansContent,
                        'is_correct' => ($key == $request->correct_answer)
                    ]);
                }
            } 
            // --- TRƯỜNG HỢP 2: ĐÚNG/SAI CHÙM ---
            else {
                $commonData['type'] = 'true_false_group';
                $commonData['cognitive_level_id'] = null; // Câu cha không có mức độ, mức độ nằm ở câu con
                
                $parentQ = Question::create($commonData);

                if ($request->has('sub_questions')) {
                    foreach ($request->sub_questions as $subData) {
                        Question::create([
                            'content' => $subData['content'],
                            'type' => 'true_false_item',
                            'parent_id' => $parentQ->id,
                            
                            // Kế thừa toàn bộ thông tin từ cha (bao gồm cả 2 trường mới)
                            'topic_id' => $request->topic_id,
                            'grade' => $request->grade,
                            'orientation' => $request->orientation,
                            'competency_id' => $request->competency_id,
                            'learning_objective_id' => $request->learning_objective_id, // [MỚI]
                            'core_content_id' => $request->core_content_id,             // [MỚI]
                            
                            'cognitive_level_id' => $subData['cognitive_level_id'],
                        ]);
                        
                        $childId = Question::latest('id')->first()->id;
                        $isTrueCorrect = ($subData['correct_option'] == 'true');
                        Answer::create(['question_id' => $childId, 'content' => 'Đúng', 'is_correct' => $isTrueCorrect]);
                        Answer::create(['question_id' => $childId, 'content' => 'Sai', 'is_correct' => !$isTrueCorrect]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('teacher.questions.index')->with('success', 'Thêm câu hỏi thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // [MỚI] Hàm xử lý Thêm nhanh câu hỏi qua AJAX
    public function storeQuick(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'answers' => 'required|array|min:4', 
            'correct_index' => 'required|integer', 
            'topic_id' => 'required|integer',
            'grade' => 'required|integer',
            'orientation' => 'required|string',
            'competency_id' => 'required|integer',
            'cognitive_level_id' => 'required|integer',
            // [MỚI]
            'learning_objective_id' => 'nullable|integer',
            'core_content_id' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            $question = Question::create([
                'content' => $request->content,
                'topic_id' => $request->topic_id,
                'grade' => $request->grade,
                'orientation' => $request->orientation,
                'competency_id' => $request->competency_id,
                'cognitive_level_id' => $request->cognitive_level_id,
                
                // [MỚI]
                'learning_objective_id' => $request->learning_objective_id,
                'core_content_id' => $request->core_content_id,
                
                'type' => 'single_choice',
                'status' => 'active',
            ]);

            foreach ($request->answers as $index => $content) {
                Answer::create([
                    'question_id' => $question->id,
                    'content' => $content,
                    'is_correct' => ($index == $request->correct_index),
                ]);
            }

            DB::commit();
            $question->load(['topic', 'competency', 'cognitiveLevel']);

            return response()->json([
                'success' => true, 
                'message' => 'Thêm thành công!', 
                'data' => $question
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 4. Xóa câu hỏi
    public function destroy($id)
    {
        try {
            $question = Question::findOrFail($id);
            // Xóa đáp án
            $question->answers()->delete();
            
            // Nếu là câu chùm, xóa câu con
            if ($question->type == 'true_false_group') {
                $question->children()->each(function($child) {
                    $child->answers()->delete();
                    $child->delete();
                });
            }

            $question->delete();
            return redirect()->back()->with('success', 'Đã xóa câu hỏi thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể xóa: ' . $e->getMessage());
        }
    }

    // 5. Hiển thị form sửa
    public function edit($id)
    {
        $question = Question::with(['answers', 'children'])->findOrFail($id);
        
        // [MỚI] Load kèm quan hệ để fill vào dropdown
        $topics = Topic::with(['learningObjectives', 'coreContents'])->get();
        $competencies = Competency::all();
        $levels = CognitiveLevel::all();
        
        return view('teacher.questions.edit', compact('question', 'topics', 'competencies', 'levels'));
    }

    // 6. Cập nhật
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $rules = [
            'content' => 'required',
            'topic_id' => 'required',
            'grade' => 'required',
            'orientation' => 'required',
            'competency_id' => 'required',
            // [MỚI]
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'core_content_id' => 'nullable|exists:core_contents,id',
        ];

        if ($question->type == 'single_choice') {
            $rules['cognitive_level_id'] = 'required';
        } else {
            $rules['sub_questions.*.content'] = 'required';
            $rules['sub_questions.*.cognitive_level_id'] = 'required';
        }
        
        $request->validate($rules);

        DB::beginTransaction();
        try {
            // Cập nhật câu hỏi cha (hoặc câu đơn)
            $question->update([
                'content' => $request->content,
                'topic_id' => $request->topic_id,
                'grade' => $request->grade,
                'orientation' => strtolower($request->orientation),
                'competency_id' => $request->competency_id,
                'cognitive_level_id' => ($question->type == 'single_choice') ? $request->cognitive_level_id : null,
                
                // [MỚI] Update 2 trường mới
                'learning_objective_id' => $request->learning_objective_id,
                'core_content_id' => $request->core_content_id,
            ]);

            // Cập nhật câu trắc nghiệm
            if ($question->type == 'single_choice') {
                if ($request->has('answers')) {
                    $existingAnswers = $question->answers()->orderBy('id')->get();
                    foreach ($request->answers as $key => $ansContent) {
                        if (isset($existingAnswers[$key])) {
                            $existingAnswers[$key]->update([
                                'content' => $ansContent,
                                'is_correct' => ($key == $request->correct_answer)
                            ]);
                        }
                    }
                }
            } 
            // Cập nhật câu đúng sai chùm
            elseif ($question->type == 'true_false_group') {
                $children = $question->children()->orderBy('id')->get();
                if ($request->has('sub_questions')) {
                    foreach ($request->sub_questions as $index => $subData) {
                        if (isset($children[$index])) {
                            $childQ = $children[$index];
                            $childQ->update([
                                'content' => $subData['content'],
                                'topic_id' => $request->topic_id,
                                'grade' => $request->grade,
                                'orientation' => $request->orientation,
                                'competency_id' => $request->competency_id,
                                'cognitive_level_id' => $subData['cognitive_level_id'],
                                
                                // [MỚI] Cập nhật cho câu con luôn
                                'learning_objective_id' => $request->learning_objective_id,
                                'core_content_id' => $request->core_content_id,
                            ]);

                            $trueAns = $childQ->answers()->where('content', 'Đúng')->first();
                            $falseAns = $childQ->answers()->where('content', 'Sai')->first();
                            
                            $isTrueCorrect = ($subData['correct_option'] == 'true');
                            
                            if ($trueAns) $trueAns->update(['is_correct' => $isTrueCorrect]);
                            if ($falseAns) $falseAns->update(['is_correct' => !$isTrueCorrect]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('teacher.questions.index')->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            // 1. Validate file
            $request->validate([
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Tối đa 2MB
            ]);

            // 2. Lưu file vào thư mục public/uploads/questions
            $file = $request->file('upload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Lưu vào storage/app/public/uploads/questions
            $path = $file->storeAs('uploads/questions', $fileName, 'public');

            // 3. Trả về URL cho CKEditor
            return response()->json([
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}