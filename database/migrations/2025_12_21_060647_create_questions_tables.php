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
    Schema::create('questions', function (Blueprint $table) {
        $table->id();
        $table->text('content');
        // single_choice: Dạng 1 (4 chọn 1)
        // true_false_group: Dạng 2 (Đoạn văn dẫn)
        // true_false_item: Dạng 2 (Ý con Đúng/Sai)
        $table->enum('type', ['single_choice', 'true_false_group', 'true_false_item']);
        
        // Đệ quy cho câu hỏi chùm
        $table->unsignedBigInteger('parent_id')->nullable();
        
        // Khóa ngoại phân loại
        $table->foreignId('topic_id')->nullable()->constrained();
        $table->foreignId('competency_id')->nullable()->constrained();
        $table->foreignId('cognitive_level_id')->nullable()->constrained();
        
        $table->timestamps();
    });

    Schema::create('answers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('question_id')->constrained()->onDelete('cascade');
        $table->string('content'); // Nội dung đáp án A, B, C, D hoặc "Đúng/Sai"
        $table->boolean('is_correct')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions_tables');
    }
};
