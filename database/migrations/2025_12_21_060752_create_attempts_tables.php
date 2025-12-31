<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('exam_attempts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users'); // Học sinh
        $table->foreignId('exam_id')->constrained('exams');
        $table->dateTime('started_at');
        $table->dateTime('submitted_at')->nullable();
        $table->float('total_score')->nullable(); // Điểm tổng kết
        $table->timestamps();
    });

    Schema::create('attempt_answers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
        $table->foreignId('question_id')->constrained('questions');
        $table->foreignId('selected_answer_id')->nullable()->constrained('answers');
        
        // Lưu trạng thái đúng sai ngay lúc nộp để truy vấn thống kê nhanh
        // Tránh việc phải join lại bảng answers mỗi khi cần report
        $table->boolean('is_correct'); 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempts_tables');
    }
};
