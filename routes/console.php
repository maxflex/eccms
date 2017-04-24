<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');


Artisan::command('seed:courses', function () {
    $subjects = dbFactory('subjects')->get();
    $html = \DB::table('pages')->whereId(174)->value('html');
    foreach($subjects as $subject) {
        \App\Models\Page::create([
            'keyphrase' => 'Лэндинг ОГЭ – ' . mb_ucfirst($subject->name),
            'title'     => 'Лэндинг ОГЭ – ' . mb_ucfirst($subject->name),
            'url' => 'landing/oge/' . $subject->eng,
            'published' => 1,
            'h1' => 'Варианты подготовки к ОГЭ по ' . $subject->dative,
            'html' => $html,
            'group_id' => 4
        ]);
    }
})->describe('Seed courses pages');
