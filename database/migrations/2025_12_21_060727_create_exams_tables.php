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
    Schema::create('exams', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        // Giả sử có bảng users, creator_id là giáo viên
        $table->foreignId('creator_id')->constrained('users'); 
        $table->integer('duration'); // Phút
        $table->integer('total_questions')->default(0);
        $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
        $table->timestamps();
    });

    // Bảng trung gian Đề thi - Câu hỏi
    Schema::create('exam_questions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('exam_id')->constrained()->onDelete('cascade');
        $table->foreignId('question_id')->constrained()->onDelete('cascade');
        $table->integer('order');
        $table->float('score_weight')->default(0.25); // Điểm số tối đa của câu này
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams_tables');
    }
};
