<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('questions', function (Blueprint $table) {
        // grade: 10, 11, 12. Mặc định 10.
        $table->tinyInteger('grade')->default(10)->after('id');

        // orientation: chung, ict, cs.
        $table->string('orientation')->default('chung')->after('grade');
    });
}

public function down(): void
{
    Schema::table('questions', function (Blueprint $table) {
        $table->dropColumn(['grade', 'orientation']);
    });
}
};
