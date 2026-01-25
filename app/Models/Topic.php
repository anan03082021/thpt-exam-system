<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    // [CHỈNH SỬA] Bỏ 'grade' và 'orientation' vì bảng topics của bạn không có 2 cột này
    protected $fillable = ['name', 'parent_id']; 

    // 1. Quan hệ: Một Chủ đề có nhiều Nội dung cốt lõi
    // (Đây là hàm quan trọng nhất để bộ lọc hoạt động)
    public function coreContents()
    {
        return $this->hasMany(CoreContent::class);
    }

    // 2. Quan hệ: Một Chủ đề có nhiều Câu hỏi
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    
    // (Tùy chọn) Nếu bảng learning_objectives có cột topic_id thì giữ, không thì xóa
    public function learningObjectives()
    {
        return $this->hasMany(LearningObjective::class);
    }
}