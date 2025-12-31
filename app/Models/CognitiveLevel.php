<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CognitiveLevel extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'name']; // Cho phép ghi ID và Name
    public $timestamps = false; // Nếu bảng không có cột created_at/updated_at thì thêm dòng này
}