<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositionColumnPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->integer('position')->unsigned();
        });
        Schema::table('page_groups', function (Blueprint $table) {
            $table->integer('position')->unsigned();
        });

        \App\Models\PageGroup::all()->each(function($model, $index) {
            $model->position = $index;
            $model->save();
        });

        $id = \App\Models\PageGroup::create(['title' => 'Остальные'])->id;
        \App\Models\Page::whereNull('group_id')->update(['group_id' => $id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('position');
        });
        Schema::table('page_groups', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
}
