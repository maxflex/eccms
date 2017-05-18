<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhotoGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photo_groups', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('title', 255);
            $table->integer('position')->unsigned();
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->integer('group_id')->unsigned()->nullable();
            $table->foreign('group_id')->references('id')->on('photo_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photo_groups');
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('group_id');
        });
    }
}
