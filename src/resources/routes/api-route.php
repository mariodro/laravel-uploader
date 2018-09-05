<?php


Route::group([
    'middleware' => 'auth:api',
], function () {
    Route::get('/{uploader}', 'UploaderController@download')->name('download');
});
