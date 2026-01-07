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
    Schema::table('exams', function (Blueprint $table) {
        // Thêm cột is_public: 1 là công khai, 0 là riêng tư
        $table->boolean('is_public')->default(false)->after('duration');
        // Có thể xóa cột password nếu không dùng nữa
        // $table->dropColumn('password'); 
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            //
        });
    }
};
