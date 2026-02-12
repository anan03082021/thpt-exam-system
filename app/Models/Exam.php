<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\ExamAttempt; 
use App\Models\User;  // Thêm import
use App\Models\Topic; // Thêm import

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description', // <--- THÊM DÒNG NÀY ĐỂ LƯU MÔ TẢ
        'creator_id',
        'duration',
        'status',
        'total_questions',
        'is_public'
    ];

    /**
     * LOGIC TỰ ĐỘNG XÓA DỮ LIỆU LIÊN QUAN
     */
    protected static function booted()
    {
        static::deleting(function ($exam) {
            // 1. Xóa tất cả Lượt làm bài (ExamAttempt)
            // QUAN TRỌNG: Dùng mỗi vòng lặp để kích hoạt sự kiện deleting của từng Attempt (xóa AttemptAnswer)
            $exam->attempts->each(function ($attempt) {
                $attempt->delete();
            });

            // 2. Gỡ bỏ mối quan hệ với các câu hỏi (bảng trung gian exam_questions)
            $exam->questions()->detach();
        });
    }

    // --- CÁC MỐI QUAN HỆ (RELATIONSHIPS) ---

    // 1. Quan hệ với Câu hỏi (Many-to-Many)
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions', 'exam_id', 'question_id')
                    ->withPivot('order', 'score_weight')
                    ->withTimestamps();
    }

    // 2. Quan hệ với Người tạo
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // 3. Quan hệ với Chủ đề
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    // 4. Quan hệ với Lượt thi (ExamAttempt)
    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'exam_id'); 
    }
}