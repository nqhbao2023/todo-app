<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn(['repeat_days', 'repeat_weekday', 'repeat_day']);
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->json('repeat_days')->nullable();
            $table->unsignedTinyInteger('repeat_weekday')->nullable();
            $table->unsignedTinyInteger('repeat_day')->nullable();
        });
    }
};
