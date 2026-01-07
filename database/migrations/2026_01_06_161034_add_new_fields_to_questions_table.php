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
        // Thêm khóa ngoại, cho phép null để không lỗi dữ liệu cũ
        $table->foreignId('learning_objective_id')->nullable()->constrained('learning_objectives')->nullOnDelete();
        $table->foreignId('core_content_id')->nullable()->constrained('core_contents')->nullOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            //
        });
    }
};
