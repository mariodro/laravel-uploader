<?php

namespace Lloricode\LaravelUploader\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Uploader extends Model
{
    const UPDATED_AT = null;
    const PATH_FOLDER = 'uploaders';

    protected $fillable = [
        'user_id',
        'extension',
        'content_type',
        'path',
        'bytes',
        'disk',
        'label',
        'client_original_name',
    ];

    protected $hidden = [
        'id',
        'uploaderable_id',
        'uploaderable_type',
        'user_id',
        'path',
    ];

    protected $dates = [
        'created_at',
    ];

    /**
     * Get all of the owning uploaderable models.
     */
    public function uploaderable()
    {
        return $this->morphTo();
    }

    public function delete()
    {
        Storage::disk($this->disk)
            ->delete($this->path);

        parent::delete();
    }

    public function downloadLink($route = 'web')
    {
        return route("uploaders.$route.download", $this);
    }
}
