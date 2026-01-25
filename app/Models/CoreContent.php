<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreContent extends Model
{
    use HasFactory;

    // [CẬP NHẬT] Thêm 'grade' và 'orientation' cho khớp với Database
    protected $fillable = ['name', 'grade', 'orientation', 'topic_id'];

    // 1. Quan hệ ngược: Thuộc về 1 Chủ đề (Topic)
    public function topic() {
        return $this->belongsTo(Topic::class);
    }

    // 2. [THÊM MỚI] Quan hệ xuôi: Có nhiều Yêu cầu cần đạt
    // Hàm này rất quan trọng để lấy danh sách YCCĐ
    public function learningObjectives() {
        return $this->hasMany(LearningObjective::class);
    }
}