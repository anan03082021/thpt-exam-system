<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade', 'orientation', 'topic_id', 'content', 'explanation',
        'type', 'parent_id', 'cognitive_level_id', 'competency_id', 'learning_objective_id', 
        'orientation',
        'core_content_id'
    ];

    // --- CÁC MỐI QUAN HỆ (RELATIONSHIPS) ---

    public function learningObjective() {
        return $this->belongsTo(LearningObjective::class);
    }

    public function coreContent() {
        return $this->belongsTo(CoreContent::class);
    }

    public function topic() { 
        return $this->belongsTo(Topic::class); 
    }

    public function answers() { 
        return $this->hasMany(Answer::class); 
    }

    // Quan hệ lấy các câu hỏi con
    public function children() { 
        return $this->hasMany(Question::class, 'parent_id'); 
    }

    // --- BỔ SUNG HÀM NÀY ĐỂ SỬA LỖI ---
    // Quan hệ lấy câu hỏi cha (để hiển thị đoạn văn dẫn)
    public function parent() {
        return $this->belongsTo(Question::class, 'parent_id');
    }

    public function cognitiveLevel() {
        return $this->belongsTo(CognitiveLevel::class, 'cognitive_level_id');
    }

    public function competency() {
        return $this->belongsTo(Competency::class, 'competency_id');
    }
}