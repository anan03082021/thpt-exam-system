<?php

namespace App\Http\Controllers\Teacher; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use App\Models\Competency;
use App\Models\CognitiveLevel;

class QuestionController extends Controller
{
    // 1. Hiển thị danh sách câu hỏi
    public function index()
    {
        // Lấy câu hỏi đơn hoặc câu cha của nhóm
        $questions = Question::with('topic')
            ->whereIn('type', ['single_choice', 'true_false_group'])
            ->orderByDesc('id')
            ->paginate(10);
            
        return view('teacher.questions.index', compact('questions'));
    }

    // 2. Hiển thị form tạo mới
public function create()
    {
        // 1. Lấy danh sách Chủ đề
        $topics = Topic::all();

        // 2. Lấy danh sách Năng lực (Competency)
        $competencies = Competency::all();

        // 3. Lấy danh sách Mức độ (CognitiveLevel) -> Đây là biến $levels bạn đang thiếu
        $levels = CognitiveLevel::all();
        
        // 4. Truyền tất cả sang View bằng hàm compact()
        return view('teacher.questions.create', compact('topics', 'competencies', 'levels'));
    }

    // 3. Xử lý lưu câu hỏi (Logic phức tạp nhất)
public function store(Request $request)
    {
        // 1. Tạo rules validate cơ bản
        $rules = [
            'content' => 'required',
            'topic_id' => 'required',
            'grade' => 'required',
            'orientation' => 'required',
            'competency_id' => 'required', // Năng lực vẫn chung
            'type' => 'required'
        ];

        // 2. Validate động theo loại câu hỏi
        if ($request->type == 'single_choice') {
            $rules['cognitive_level_id'] = 'required'; // Dạng 1 bắt buộc có mức độ chung
        } else {
            // Dạng 2: Bắt buộc từng ý con phải có nội dung và mức độ
            $rules['sub_questions.*.content'] = 'required';
            $rules['sub_questions.*.cognitive_level_id'] = 'required';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            // Dữ liệu chung cho Parent
            $commonData = [
                'content' => $request->content,
                'topic_id' => $request->topic_id,
                'grade' => $request->grade,
                'orientation' => $request->orientation,
                'competency_id' => $request->competency_id,
            ];

            // --- TRƯỜNG HỢP 1: DẠNG TRẮC NGHIỆM ---
            if ($request->type == 'single_choice') {
                $commonData['type'] = 'single_choice';
                $commonData['cognitive_level_id'] = $request->cognitive_level_id; // Lưu mức độ vào cha
                
                $question = Question::create($commonData);

                // Lưu đáp án
                foreach ($request->answers as $key => $ansContent) {
                    Answer::create([
                        'question_id' => $question->id,
                        'content' => $ansContent,
                        'is_correct' => ($key == $request->correct_answer)
                    ]);
                }
            } 
            // --- TRƯỜNG HỢP 2: DẠNG ĐÚNG/SAI CHÙM ---
            else {
                $commonData['type'] = 'true_false_group';
                $commonData['cognitive_level_id'] = null; // Cha KHÔNG CÓ mức độ
                
                $parentQ = Question::create($commonData);

                if ($request->has('sub_questions')) {
                    foreach ($request->sub_questions as $subData) {
                        // Tạo câu hỏi con (Mỗi con có Level riêng)
                        Question::create([
                            'content' => $subData['content'],
                            'type' => 'true_false_item',
                            'parent_id' => $parentQ->id,
                            
                            // Các thông tin kế thừa từ cha
                            'topic_id' => $request->topic_id,
                            'grade' => $request->grade,
                            'orientation' => $request->orientation,
                            'competency_id' => $request->competency_id,

                            // THÔNG TIN RIÊNG CỦA CON
                            'cognitive_level_id' => $subData['cognitive_level_id'] 
                        ]);
                        
                        // Lưu đáp án đúng/sai...
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

    public function destroy($id)
    {
        try {
            $question = Question::findOrFail($id);
            
            // Xóa các câu trả lời liên quan trước (nếu chưa set cascade database)
            $question->answers()->delete();
            
            // Nếu là câu chùm (D2), xóa các câu con
            if ($question->type == 'true_false_group') {
                $question->children()->each(function($child) {
                    $child->answers()->delete();
                    $child->delete();
                });
            }

            // Xóa câu hỏi chính
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
        $topics = Topic::all();
        $competencies = Competency::all(); // Thêm dòng này
        $levels = CognitiveLevel::all();   // Thêm dòng này
        
        return view('teacher.questions.edit', compact('question', 'topics', 'competencies', 'levels'));
    }

    // 6. Lưu thay đổi
public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        // 1. Validate
        $rules = [
            'content' => 'required',
            'topic_id' => 'required',
            'grade' => 'required',
            'orientation' => 'required',
            'competency_id' => 'required'
        ];

        // Validate riêng theo loại câu hỏi hiện tại
        if ($question->type == 'single_choice') {
            $rules['cognitive_level_id'] = 'required';
        } else {
            $rules['sub_questions.*.content'] = 'required';
            $rules['sub_questions.*.cognitive_level_id'] = 'required';
        }
        
        $request->validate($rules);

        DB::beginTransaction();
        try {
            // 2. Cập nhật thông tin chung cho câu hỏi cha
            // Lưu ý: Nếu là Dạng 2, cognitive_level_id của cha sẽ là NULL
            $question->update([
                'content' => $request->content,
                'topic_id' => $request->topic_id,
                'grade' => $request->grade,
                'orientation' => $request->orientation,
                'competency_id' => $request->competency_id,
                'cognitive_level_id' => ($question->type == 'single_choice') ? $request->cognitive_level_id : null
            ]);

            // 3. Xử lý chi tiết theo loại
            if ($question->type == 'single_choice') {
                // Cập nhật đáp án (Dạng 1) - Logic cũ dùng update đè
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
            elseif ($question->type == 'true_false_group') {
                // Cập nhật câu hỏi con (Dạng 2)
                $children = $question->children()->orderBy('id')->get();
                
                if ($request->has('sub_questions')) {
                    foreach ($request->sub_questions as $index => $subData) {
                        if (isset($children[$index])) {
                            $childQ = $children[$index];

                            // Cập nhật thông tin câu con
                            // Quan trọng: Phải đồng bộ Grade/Orientation/Topic theo Cha + Cập nhật Level riêng
                            $childQ->update([
                                'content' => $subData['content'],
                                'topic_id' => $request->topic_id,     // Đồng bộ
                                'grade' => $request->grade,           // Đồng bộ
                                'orientation' => $request->orientation, // Đồng bộ
                                'competency_id' => $request->competency_id, // Đồng bộ
                                'cognitive_level_id' => $subData['cognitive_level_id'] // CẬP NHẬT MỨC ĐỘ RIÊNG
                            ]);

                            // Cập nhật đáp án Đúng/Sai
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
}