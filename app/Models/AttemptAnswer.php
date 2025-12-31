<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttemptAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_answer_id',
        'is_correct'
    ];

    // Quan hệ với Lượt thi
    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    // Quan hệ với Câu hỏi (Để lấy nội dung câu hỏi, chủ đề...)
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Quan hệ với Đáp án đã chọn (Để hiển thị text "Bạn chọn: ...")
    public function selectedAnswer()
    {
        return $this->belongsTo(Answer::class, 'selected_answer_id');
    }
}