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
    Schema::create('documents', function (Blueprint $table) {
        $table->id();
        $table->string('title');            // Tên hiển thị của tài liệu
        $table->string('file_path');        // Đường dẫn file lưu trong server
        $table->string('file_type')->nullable(); // Loại file (pdf, docx...)
        $table->unsignedBigInteger('file_size')->nullable(); // Dung lượng (KB)
        
        // Liên kết với chủ đề
        $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
        
        // Người upload (Giáo viên)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
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
