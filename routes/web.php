<?php
URL::forceSchema('https');

# Login
Route::post('login', 'LoginController@login');
Route::get('logout', 'LoginController@logout');


Route::group(['middleware' => ['login']], function () {
    # Variables
    Route::get('/', 'VariablesController@index');
    Route::resource('variables', 'VariablesController');

    # Pages
    Route::get('pages/export', 'PagesController@export')->name('pages.export');
    Route::post('pages/import', 'PagesController@import')->name('pages.import');
    Route::resource('pages', 'PagesController');
    Route::get('search', 'PagesController@search');

    Route::resource('programs', 'ProgramsController');

    Route::get('sass/{file}', 'SassController@edit')->where('file', '.*.scss$');
    Route::get('sass/{directory?}', 'SassController@index')->where('directory', '.*');

    Route::resource('photos', 'PhotosController');

    Route::resource('faq', 'FaqController');
    # Templates for angular directives
    Route::get('directives/{directive}', function($directive) {
        return view("directives.{$directive}");
    });
});
