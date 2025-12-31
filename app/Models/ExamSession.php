<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamSession extends Model
{
    use HasFactory; // Bạn đã import ở trên nhưng quên dùng ở đây, tôi đã thêm vào

    protected $fillable = ['title', 'exam_id', 'start_at', 'end_at'];

    // --- QUAN TRỌNG: THÊM ĐOẠN NÀY ĐỂ SỬA LỖI ---
    // Giúp Laravel tự động chuyển đổi chuỗi ngày tháng thành đối tượng Carbon
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
    // ---------------------------------------------

    public function exam() {
        return $this->belongsTo(Exam::class);
    }

    public function students() {
        return $this->hasMany(ExamSessionStudent::class);
    }

    // Thêm quan hệ này để lấy danh sách các lượt thi trong ca này
    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'exam_session_id');
    }
}