<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningObjective extends Model
{
    use HasFactory;

    // [CẬP NHẬT] Thêm 'core_content_id' vào fillable để khớp với Database
    protected $fillable = ['content', 'topic_id', 'core_content_id'];

    // 1. Quan hệ chính: Thuộc về 1 Nội dung cốt lõi
    // (Đây là hàm quan trọng để lọc YCCĐ theo Nội dung)
    public function coreContent() {
        return $this->belongsTo(CoreContent::class);
    }

    // 2. Quan hệ phụ: Thuộc về 1 Chủ đề (Giữ nguyên vì DB bạn có cột topic_id)
    public function topic() {
        return $this->belongsTo(Topic::class);
    }
}