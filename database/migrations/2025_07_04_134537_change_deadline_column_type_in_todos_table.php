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
        Schema::table('todos', function (Blueprint $table) {
            // Chuyển kiểu cột từ DATE sang DATETIME
            $table->dateTime('deadline')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            // (Tuỳ chọn) trả về lại DATE nếu cần
            $table->date('deadline')->nullable()->change();
        });
    }
};
