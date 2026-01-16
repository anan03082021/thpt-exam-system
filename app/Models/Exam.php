<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Question; // Đảm bảo import Model Question


class Exam extends Model
{
    use HasFactory, SoftDeletes;

    // --- BỔ SUNG ĐOẠN NÀY ĐỂ SỬA LỖI ---
    protected $fillable = [
        'title',
        'creator_id',
        'duration',
        'status',
        'creator_id', 
        'total_questions',
        'is_public'
    ];
    // -----------------------------------

    // Quan hệ: Một đề thi có nhiều câu hỏi (thông qua bảng trung gian)
public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions', 'exam_id', 'question_id')
                    ->withPivot('order', 'score_weight') // Lấy dữ liệu cột phụ
                    ->withTimestamps(); // Quan trọng: Tự động điền created_at/updated_at trong bảng pivot
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function topic()
    {
        // Lưu ý: Lệnh này chỉ chạy được nếu bảng 'exams' trong Database
        // ĐÃ CÓ cột 'topic_id'. Nếu chưa có, nó sẽ báo lỗi "Column not found".
        return $this->belongsTo(Topic::class);
    }
}