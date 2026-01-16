<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cập nhật bảng core_contents (Nội dung cốt lõi)
        Schema::table('core_contents', function (Blueprint $table) {
            // Thêm cột grade nếu chưa có
            if (!Schema::hasColumn('core_contents', 'grade')) {
                $table->tinyInteger('grade')->default(10)->after('name'); 
                // Dùng default(10) để dữ liệu cũ không bị lỗi
            }
            // Thêm cột orientation nếu chưa có
            if (!Schema::hasColumn('core_contents', 'orientation')) {
                $table->string('orientation')->default('chung')->after('grade');
            }
        });

        // 2. Cập nhật bảng learning_objectives (Yêu cầu cần đạt)
        Schema::table('learning_objectives', function (Blueprint $table) {
            if (!Schema::hasColumn('learning_objectives', 'core_content_id')) {
                // Thêm cột mới và cho phép NULL (nullable) để không xung đột với dữ liệu cũ
                $table->foreignId('core_content_id')
                      ->nullable() 
                      ->after('id')
                      ->constrained('core_contents')
                      ->onDelete('cascade');
            }
        });
    }
    
    public function down(): void
    {
        // Định nghĩa cách xóa cột nếu muốn rollback
        Schema::table('core_contents', function (Blueprint $table) {
            $table->dropColumn(['grade', 'orientation']);
        });

        Schema::table('learning_objectives', function (Blueprint $table) {
            $table->dropForeign(['core_content_id']);
            $table->dropColumn('core_content_id');
        });
    }
};