<?php

Route::group([
    'middleware' => 'auth',
], function () {
    Route::get('/{uploader}', 'UploaderController@download')->name('download');
    Route::delete('/{uploader}', 'UploaderController@delete')->name('delete');
});
