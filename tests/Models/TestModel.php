<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lloricode\LaravelUploader\Contract\UploaderContract;
use Lloricode\LaravelUploader\UploaderOptions;
use Lloricode\LaravelUploader\Traits\UploaderTrait;

class TestModel extends Model implements UploaderContract
{
    use UploaderTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function uploaderRules() :UploaderOptions // <--------  add
    {
        return UploaderOptions::create()
            ->fileNamePrefix('file-')
            ->disk('local') // any disk in config/filesystems.disk
            ->maxSize(20000000); // byte in decimal = 20mb
    }
}
