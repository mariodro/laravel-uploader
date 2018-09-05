<?php


Route::group([
    
], function () {
    Route::get('/{uploader}', 'UploaderController@download')->name('download');
});
