<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_add_attachment_link_to_todos_table.php
public function up()
{
    Schema::table('todos', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->string('attachment_link', 500)->nullable()->after('detail');
    });
}
public function down()
{
    Schema::table('todos', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->dropColumn('attachment_link');
    });
}

};
