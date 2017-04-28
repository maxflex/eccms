<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('version_control', function (Blueprint $table) {
            $table->string('table');
            $table->string('column');
            $table->string('entity_id');
            $table->string('md5');
            $table->index(['table', 'entity_id']);
            // $table->primary(['table', 'entity_id', 'column']);
        });
        shell_exec('php artisan generate:version_control');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('version_control');
    }
}
