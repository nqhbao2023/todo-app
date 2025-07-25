<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('todos', function (Blueprint $table) {
            if (Schema::hasColumn('todos', 'repeat_days')) {
                $table->dropColumn('repeat_days');
            }
            if (Schema::hasColumn('todos', 'repeat_weekday')) {
                $table->dropColumn('repeat_weekday');
            }
            if (Schema::hasColumn('todos', 'repeat_day')) {
                $table->dropColumn('repeat_day');
            }
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
