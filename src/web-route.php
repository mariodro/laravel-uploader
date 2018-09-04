<?php


Route::group([
    'namespace' => 'Lloricode\LaravelUploader\Http\Controllers',
    'middleware' => 'auth',
    'as' => 'uploader.'
], function () {
    Route::get('/{uploader}', 'UploaderController@index')->name('download');
});
