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
        'submitted_at' => 'datetime', // Dòng này giúp Laravel hiểu đây là ngày tháng
    ];

    protected $fillable = [
        'user_id', 
        'exam_id', 
        'exam_session_id',
        'started_at', 
        'submitted_at',
        'total_score'
    ];

    public function examSession() {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    // --- QUAN TRỌNG: ĐỔI TÊN HÀM TỪ answers THÀNH attemptAnswers ---
    public function attemptAnswers()
    {
        // Khóa ngoại là 'attempt_id'
        return $this->hasMany(AttemptAnswer::class, 'attempt_id');
    }
    // ---------------------------------------------------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}