<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmUpdateTeacherReviewsGrade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::connection('egecrm')->table('teacher_reviews')->whereIn('grade', [9, 10])->update([
            'max_score' => 5
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_reviews', function (Blueprint $table) {
            //
        });
    }
}
