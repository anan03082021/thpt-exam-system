<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = ['name', 'parent_id'];

    // Quan hệ: Một chủ đề có nhiều câu hỏi
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    
    // Đệ quy chủ đề (Ví dụ: Chương 1 -> Bài 1)
    public function children()
    {
        return $this->hasMany(Topic::class, 'parent_id');
    }
}
