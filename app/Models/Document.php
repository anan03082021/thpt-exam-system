<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    // Khai báo đúng các cột như trong ảnh Database của bạn + cột grade mới thêm
    protected $fillable = [
        'title', 
        'file_path', 
        'file_type', 
        'file_size', 
        'topic_id', 
        'user_id', 
        'grade' // Cột mới thêm
    ];

    // --- CÁC HÀM PHỤ TRỢ CHO VIEW (Giữ nguyên để hiển thị Icon đẹp) ---

    // 1. Tạo class màu sắc dựa trên đuôi file
    public function getIconClassAttribute()
    {
        return match(strtolower($this->file_type)) {
            'pdf' => 'icon-pdf',
            'doc', 'docx' => 'icon-word',
            'ppt', 'pptx' => 'icon-ppt',
            default => 'bg-gray-100 text-gray-500', // Mặc định xám
        };
    }

    // 2. Tạo icon Bootstrap dựa trên đuôi file
    public function getIconHtmlAttribute()
    {
        return match(strtolower($this->file_type)) {
            'pdf' => 'bi-file-earmark-pdf-fill',
            'doc', 'docx' => 'bi-file-earmark-word-fill',
            'ppt', 'pptx' => 'bi-file-earmark-slides-fill',
            default => 'bi-file-earmark-text',
        };
    }
}