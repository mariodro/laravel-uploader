<?php


Route::group([
    'middleware' => 'auth',
    'as' => 'uploader.'
], function () {
    Route::get('/{uploader}', 'UploaderController@download')->name('download');
});
