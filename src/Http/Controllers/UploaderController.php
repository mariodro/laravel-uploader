<?php

namespace Lloricode\LaravelUploader\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class UploaderController extends Controller
{
    public function download($uploader)
    {
        $uploader = Config::get('uploader.implementation', \Lloricode\LaravelUploader\Models\Uploader::class)::findOrFail($uploader);
        
        $uploderable = $uploader->uploaderable;
        $uploderableRules = $uploderable->uploaderRules();

        $label = $uploader->label?:$uploderableRules->fileNamePrefix . now()->format('Ymd_Hi');

        return Storage::disk($uploader->disk)->download(
            $uploader->path,
            $label  . '.' . $uploader->extension,
            [
                'Content-Type: ' . $uploader->content_type
            ]
        );
    }

    public function delete($uploader)
    {
        $uploader = Config::get('uploader.implementation', \Lloricode\LaravelUploader\Models\Uploader::class)::findOrFail($uploader);
        $uploader->delete();
        return response()->json([], 204);
    }
}
