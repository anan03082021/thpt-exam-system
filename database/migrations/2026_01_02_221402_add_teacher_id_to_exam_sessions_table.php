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
    Schema::table('exam_sessions', function (Blueprint $table) {
        // Thêm cột teacher_id, nên đặt sau exam_id hoặc password
        $table->unsignedBigInteger('teacher_id')->nullable()->after('password');
        
        // (Tùy chọn) Nếu muốn khóa ngoại để liên kết với bảng users
        // $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            //
        });
    }
};
