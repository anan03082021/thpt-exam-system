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
    Schema::create('learning_objectives', function (Blueprint $table) {
        $table->id();
        $table->text('content'); // Nội dung yêu cầu (VD: Hiểu được nguyên lý...)
        // Liên kết với chủ đề (Topic) để dễ lọc
        $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_objectives');
    }
};
