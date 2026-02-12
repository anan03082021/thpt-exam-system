<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttemptAnswer; 

class ExamAttempt extends Model
{
    use HasFactory;

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id', 
        'exam_id', 
        'exam_session_id',
        'started_at', 
        'submitted_at',
        'total_score'
    ];

    /**
     * --- [QUAN TRỌNG] LOGIC TỰ ĐỘNG XÓA CON ---
     * Hàm này sẽ chạy khi bạn thực hiện lệnh $attempt->delete()
     */
    protected static function booted()
    {
        static::deleting(function ($attempt) {
            // Khi xóa một Lượt thi, xóa sạch các Câu trả lời chi tiết của lượt đó trước
            // để tránh lỗi khóa ngoại (Constraint fails)
            $attempt->attemptAnswers()->delete();
        });
    }

    // --- CÁC MỐI QUAN HỆ ---

    public function examSession() {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function attemptAnswers()
    {
        return $this->hasMany(AttemptAnswer::class, 'attempt_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}