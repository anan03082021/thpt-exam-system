<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('questions', function (Blueprint $table) {
        $table->unsignedBigInteger('cognitive_level_id')->nullable()->change();
    });
}

public function down(): void
{
    // Lưu ý: Rollback có thể lỗi nếu dữ liệu đang có null
    Schema::table('questions', function (Blueprint $table) {
        $table->unsignedBigInteger('cognitive_level_id')->nullable(false)->change();
    });
}
};
