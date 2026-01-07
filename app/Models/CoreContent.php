<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- BẮT BUỘC PHẢI CÓ DÒNG NÀY
use Illuminate\Database\Eloquent\Model;

class CoreContent extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'topic_id'];

    public function topic() {
        return $this->belongsTo(Topic::class);
    }
}