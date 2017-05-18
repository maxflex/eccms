<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePhotosGroupId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $other_group_id = \DB::table('photo_groups')->insertGetId([
            'title' => 'Остальные',
            'position' => 99,
        ]);

        \DB::table('photos')->whereNull('group_id')->update([
            'group_id' => $other_group_id
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
