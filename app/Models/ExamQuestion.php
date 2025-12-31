<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use HasFactory;

    // Chỉ định rõ tên bảng (nếu không Laravel sẽ tự tìm bảng số nhiều là 'exam_questions' - nhưng khai báo rõ thì tốt hơn)
    protected $table = 'exam_questions';

    // Cho phép ghi dữ liệu vào các cột này
    protected $fillable = [
        'exam_id',
        'question_id',
        'order',
        'score_weight'
    ];
}