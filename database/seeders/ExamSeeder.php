<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamQuestion;
use Illuminate\Support\Facades\Hash;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
// 1. Tạo tài khoản GIÁO VIÊN (ID = 1)
    if (!User::where('email', 'teacher@example.com')->exists()) {
        User::create([
            'id' => 1,
            'name' => 'Cô Giáo Tin',
            'email' => 'teacher@example.com',
            'password' => Hash::make('12345678'), // Mật khẩu dễ nhớ để test
            'role' => 'admin' // <--- Role Giáo viên
        ]);
    }

    // 2. Tạo tài khoản HỌC SINH (ID = 2)
    if (!User::where('email', 'student@example.com')->exists()) {
        User::create([
            'id' => 2,
            'name' => 'Em Học Sinh',
            'email' => 'student@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'student' // <--- Role Học sinh
        ]);
    }

        // 2. Tạo một Đề thi mẫu có ID = 1
        if (!Exam::where('id', 1)->exists()) {
            $exam = Exam::create([
                'id' => 1,
                'title' => 'Đề thi thử Tốt nghiệp THPT 2025 - Tin học',
                'creator_id' => 1, // Người tạo là User ID 1
                'duration' => 50,
                'status' => 'published',
                'total_questions' => 0
            ]);

            // 3. Lấy các câu hỏi đã có trong ngân hàng để đưa vào đề thi này
            $questions = Question::whereIn('type', ['single_choice', 'true_false_group'])->get();
            
            $order = 1;
            foreach ($questions as $q) {
                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_id' => $q->id,
                    'order' => $order++,
                    'score_weight' => ($q->type == 'single_choice') ? 0.25 : 1.0
                ]);
            }
            
            // Cập nhật lại tổng số câu hỏi
            $exam->update(['total_questions' => $questions->count()]);
        }
    }
}