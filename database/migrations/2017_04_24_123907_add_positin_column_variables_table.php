<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositinColumnVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('variables', function (Blueprint $table) {
            $table->integer('position')->unsigned();
        });
        Schema::table('variable_groups', function (Blueprint $table) {
            $table->integer('position')->unsigned();
        });

        \App\Models\VariableGroup::all()->each(function($model, $index) {
            $model->position = $index;
            $model->save();
        });

        $id = \App\Models\VariableGroup::create(['title' => 'Остальные'])->id;
        \App\Models\Variable::whereNull('group_id')->update(['group_id' => $id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variables', function (Blueprint $table) {
            $table->dropColumn('position');
        });
        Schema::table('variable_groups', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
}
