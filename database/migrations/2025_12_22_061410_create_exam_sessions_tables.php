<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    // Bảng Ca thi
    Schema::create('exam_sessions', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // Tên ca thi (VD: Thi Giữa Kỳ Lớp 10A)
        $table->foreignId('exam_id')->constrained()->onDelete('cascade'); // Link tới đề thi gốc
        $table->dateTime('start_at'); // Thời gian bắt đầu
        $table->dateTime('end_at');   // Thời gian kết thúc
        $table->timestamps();
    });

    // Bảng Danh sách học sinh được phép thi
    Schema::create('exam_session_students', function (Blueprint $table) {
        $table->id();
        $table->foreignId('exam_session_id')->constrained()->onDelete('cascade');
        $table->string('student_email');
        $table->string('student_name');
        // user_id có thể null nếu học sinh chưa tạo tài khoản, sau này sẽ map sau
        $table->unsignedBigInteger('user_id')->nullable(); 
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('exam_session_students');
    Schema::dropIfExists('exam_sessions');
}
};
