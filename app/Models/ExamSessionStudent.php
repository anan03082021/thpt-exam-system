<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ExamSessionStudent extends Model
{
    protected $fillable = ['exam_session_id', 'student_email', 'student_name', 'user_id'];
}