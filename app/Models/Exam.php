<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question; // Đảm bảo import Model Question

class Exam extends Model
{
    use HasFactory;

    // --- BỔ SUNG ĐOẠN NÀY ĐỂ SỬA LỖI ---
    protected $fillable = [
        'title',
        'creator_id',
        'duration',
        'status',
        'total_questions'
    ];
    // -----------------------------------

    // Quan hệ: Một đề thi có nhiều câu hỏi (thông qua bảng trung gian)
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
                    ->withPivot('order', 'score_weight')
                    ->orderBy('exam_questions.order');
    }

    public function topic()
    {
        // Lưu ý: Lệnh này chỉ chạy được nếu bảng 'exams' trong Database
        // ĐÃ CÓ cột 'topic_id'. Nếu chưa có, nó sẽ báo lỗi "Column not found".
        return $this->belongsTo(Topic::class);
    }
}