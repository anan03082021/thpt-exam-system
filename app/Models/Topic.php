<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'grade', 'orientation']; // Ví dụ các cột cũ của bạn

    // --- CÁC QUAN HỆ CŨ (Nếu có) ---
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // --- BỔ SUNG 2 HÀM NÀY ĐỂ SỬA LỖI ---
    
    // 1. Quan hệ với Yêu cầu cần đạt
    public function learningObjectives()
    {
        return $this->hasMany(LearningObjective::class);
    }

    // 2. Quan hệ với Nội dung trọng tâm
    public function coreContents()
    {
        return $this->hasMany(CoreContent::class);
    }
}