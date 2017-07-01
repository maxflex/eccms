<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStreamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->create('stream', function (Blueprint $table) {
            $table->increments('id');
            $table->string('action');
            $table->string('type')->nullable();
            $table->integer('step')->unsigned();
            $table->string('google_id');
            $table->string('yandex_id');
            $table->boolean('mobile')->default(false);
            $table->string('href', 1000);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('egecrm')->drop('stream');
    }
}
