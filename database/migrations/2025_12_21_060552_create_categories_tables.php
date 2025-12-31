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
    // Bảng Chủ đề kiến thức
    Schema::create('topics', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->unsignedBigInteger('parent_id')->nullable(); // Để tạo cây thư mục
        $table->timestamps();
    });

    // Bảng Năng lực (NLa, NLb,...)
    Schema::create('competencies', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique(); // NLa, NLb
        $table->text('description');
        $table->timestamps();
    });

    // Bảng Mức độ nhận thức (Biết, Hiểu, Vận dụng)
    Schema::create('cognitive_levels', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories_tables');
    }
};
