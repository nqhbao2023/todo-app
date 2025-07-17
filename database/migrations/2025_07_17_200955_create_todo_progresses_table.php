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
        Schema::create('todo_progresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('todo_id');
            $table->date('progress_date');
            $table->integer('quantity');
            $table->timestamps();
    
            $table->foreign('todo_id')->references('id')->on('todos')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('todo_progresses');
    }
    
};
