<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Import Model ExamAttempt để dùng trong hàm booted
use App\Models\ExamAttempt; 

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'exam_id', 'teacher_id', 'start_at', 'end_at', 'password'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * =========================================================
     * [MỚI] LOGIC TỰ ĐỘNG XÓA LỊCH SỬ THI KHI XÓA KỲ THI
     * =========================================================
     */
    protected static function booted()
    {
        static::deleting(function ($session) {
            // Khi lệnh xóa Kỳ thi (Session) được gọi:
            // 1. Lấy tất cả lượt thi (attempts) của kỳ thi này
            // 2. Duyệt qua từng lượt và xóa (delete)
            // Lệnh $attempt->delete() này sẽ kích hoạt tiếp hàm booted bên ExamAttempt
            // để xóa sạch các câu trả lời chi tiết.
            if ($session->attempts) {
                $session->attempts->each(function ($attempt) {
                    $attempt->delete();
                });
            }
            
            // Nếu có bảng danh sách học sinh tham gia (exam_session_students) cũng nên xóa
            if ($session->students) {
                $session->students()->delete();
            }
        });
    }

    // --- CÁC MỐI QUAN HỆ ---

    public function exam() {
        return $this->belongsTo(Exam::class);
    }

    public function students() {
        return $this->hasMany(ExamSessionStudent::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'exam_session_id');
    }
}