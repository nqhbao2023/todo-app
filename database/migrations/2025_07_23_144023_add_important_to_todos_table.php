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
        Schema::table('todos', function (Blueprint $table) {
            $table->boolean('important')->default(false)->after('status');
        });
    }
    public function down()
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn('important');
        });
    }
    
};
