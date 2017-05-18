<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGradeToTeacherReviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('teacher_reviews', function (Blueprint $table) {
            $table->integer('grade')->unsigned()->nullable();
        });
        $teacher_reviews = dbEgecrm('teacher_reviews')->get();
        foreach($teacher_reviews as $teacher_review) {
            dbEgecrm('teacher_reviews')->whereId($teacher_review->id)->update([
                'grade' => dbEgecrm('visit_journal')->where('id_teacher', $teacher_review->id_teacher)->where('id_entity', $teacher_review->id_student)
                            ->where('id_subject', $teacher_review->id_subject)->where('year', $teacher_review->year)->where('type_entity', 'STUDENT')->value('grade')
            ]);
        }
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
