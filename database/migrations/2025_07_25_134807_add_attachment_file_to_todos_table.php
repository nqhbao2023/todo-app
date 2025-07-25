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
            $table->string('attachment_file')->nullable()->after('attachment_link');
        });
    }
    
    public function down()
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn('attachment_file');
        });
    }
    
};
