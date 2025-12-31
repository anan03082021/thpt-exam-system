<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Topic;


class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dạng câu hỏi 1: Chọn 1 đáp án đúng
        $q1 = Question::create([
            'content' => 'Để hiển thị văn bản HTML trên trình duyệt web, ta cần phải làm gì?',
            'type' => 'single_choice',
            'topic_id' => 3, // Chủ đề Mạng máy tính
        ]);

        Answer::create(['question_id' => $q1->id, 'content' => 'A. Dịch mã nguồn sang mã nhị phân', 'is_correct' => false]);
        Answer::create(['question_id' => $q1->id, 'content' => 'B. Chạy trực tiếp file HTML trên trình duyệt', 'is_correct' => true]); // Đáp án đúng [cite: 295]
        Answer::create(['question_id' => $q1->id, 'content' => 'C. Đưa mã nguồn vào máy chủ web', 'is_correct' => false]);
        Answer::create(['question_id' => $q1->id, 'content' => 'D. Sửa hết lỗi cú pháp trong văn bản HTML', 'is_correct' => false]);
        
        // Dạng câu hỏi 2: Chọn đúng/sai cho từng ý trong nhóm
        $parentQ = Question::create([
            'content' => 'Một học sinh sử dụng phần mềm thiết kế đồ họa để vẽ một logo. Logo gồm một lá cờ tổ quốc và một dòng chữ tên lớp bên dưới lá cờ. Học sinh đó tạo ngôi sao trước, sau đó tạo lá cờ hình chữ nhật, cuối cùng tạo dòng chữ. Tuy nhiên, khi tạo lá cờ thì lá cờ che mất ngôi sao. Ngoài ra, học sinh đó muốn uốn cong các cạnh của lá cờ sao cho lá cờ giống như đang bay trước gió. Dưới đây là các nhận xét về sản phẩm đồ họa nói trên.', // [cite: 235]
            'type' => 'true_false_group',
            'topic_id' => 1, 
        ]);

        $subQ1 = Question::create([
            'content' => 'a. Lá cờ được vẽ bằng công cụ tạo hình chữ nhật (từ vùng chọn hoặc đối tượng đồ họa có sẵn).', // [cite: 241]
            'type' => 'true_false_item',
            'parent_id' => $parentQ->id,
            'topic_id' => 1
        ]);

        $subQ2 = Question::create([
            'content' => 'b. Dòng chữ thuộc cả hai lớp: lớp ngôi sao và lớp lá cờ.', // [cite: 241]
            'type' => 'true_false_item',
            'parent_id' => $parentQ->id,
            'topic_id' => 1
        ]);

        $subQ3 = Question::create([
            'content' => 'c. Lớp lá cờ ở bên trên lớp ngôi sao nên che khuất lớp ngôi sao.', // [cite: 241]
            'type' => 'true_false_item',
            'parent_id' => $parentQ->id,
            'topic_id' => 1
        ]);

        $subQ4 = Question::create([
            'content' => 'd. Không thể uốn các cạnh của lá cờ theo hình dạng mong muốn.', // [cite: 241]
            'type' => 'true_false_item',
            'parent_id' => $parentQ->id,
            'topic_id' => 1
        ]);

        Answer::create(['question_id' => $subQ1->id, 'content' => 'Đúng', 'is_correct' => true]);
        Answer::create(['question_id' => $subQ1->id, 'content' => 'Sai', 'is_correct' => false]);
        Answer::create(['question_id' => $subQ2->id, 'content' => 'Đúng', 'is_correct' => false]);
        Answer::create(['question_id' => $subQ2->id, 'content' => 'Sai', 'is_correct' => true]);
        Answer::create(['question_id' => $subQ3->id, 'content' => 'Đúng', 'is_correct' => true]);
        Answer::create(['question_id' => $subQ3->id, 'content' => 'Sai', 'is_correct' => false]);
        Answer::create(['question_id' => $subQ4->id, 'content' => 'Đúng', 'is_correct' => false]);
        Answer::create(['question_id' => $subQ4->id, 'content' => 'Sai', 'is_correct' => true]);
    }
}