<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveNullGroupFromFaqGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $other_group_id = \DB::table('faq_groups')->insertGetId([
            'title' => 'Остальные',
            'position' => 99,
        ]);

        \DB::table('faqs')->whereNull('group_id')->update([
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
