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
    Schema::table('questions', function (Blueprint $table) {
        // Thêm cột source, mặc định là 'user' (của giáo viên)
        // Giá trị có thể là: 'user', 'thpt_2025', 'minh_hoa_2026'...
        $table->string('source')->default('user')->after('type')->index();
    });
}

public function down(): void
{
    Schema::table('questions', function (Blueprint $table) {
        $table->dropColumn('source');
    });
}
};
