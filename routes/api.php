<?php
URL::forceSchema('https');

Route::group(['namespace' => 'Api', 'as' => 'api.'], function () {
    # Variables
    Route::post('variables/push', 'VariablesController@push');
    Route::post('variables/pull', 'VariablesController@pull');
    Route::resource('variables', 'VariablesController');
    Route::group(['prefix' => 'variables'], function() {
        Route::resource('groups', 'VariableGroupsController');
    });

    # Pages
    Route::post('pages/checkExistance/{id?}', 'PagesController@checkExistance');
    Route::post('pages/search', 'PagesController@search');
    Route::resource('pages', 'PagesController');

    #pr
    Route::resource('programs', 'ProgramsController');

    # Translit
    Route::post('translit/to-url', 'TranslitController@toUrl');

    Route::resource('sass', 'SassController');


    Route::resource('photos/upload', 'PhotosController@upload');
    Route::resource('photos', 'PhotosController');

    Route::resource('faq', 'FaqController');
    Route::group(['prefix' => 'faq'], function() {
        Route::resource('groups', 'FaqGroupsController');
    });

    # Factory
    Route::post('factory', 'FactoryController@get');
});
