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
    Schema::create('core_contents', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Tên nội dung (VD: Mảng một chiều, Hàm...)
        $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_contents');
    }
};
