<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('documents', function (Blueprint $table) {
        $table->id();
        $table->string('title');        // Tên tài liệu
        $table->string('file_path');    // Đường dẫn file
        $table->string('file_type');    // pdf, docx, pptx
        $table->string('file_size')->nullable(); // Ví dụ: 2.4 MB
        $table->tinyInteger('grade');   // Lớp: 10, 11, 12
        $table->tinyInteger('topic_id');// Chủ đề: 1, 2, 3, 4, 5
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
