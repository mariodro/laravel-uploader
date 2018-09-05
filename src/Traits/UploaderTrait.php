<?php

namespace Lloricode\LaravelUploader\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Lloricode\LaravelUploader\Contract\UploaderContract;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

trait UploaderTrait
{

    /**
     * Return Uploader relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function uploaders() :MorphMany
    {
        return $this->morphMany(Config::get('uploader.implementation', \Lloricode\LaravelUploader\Models\Uploader::class), 'uploaderable');
    }

    public function delete()
    {
        foreach ($this->uploaders as $uploader) {
            
            // delete file
            $uploader->delete();
        }

        parent::delete();
    }

    public function getUploadedFiles() :Collection
    {
        $return = collect([]);
        foreach ($this->uploaders as $uploader) {
            $return->push((object)[
                'path' => $uploader->path,
                'client_original_name' => $uploader->client_original_name,
                'label' => $uploader->label,
                'extension' => $uploader->extension,
                'disk' => $uploader->disk,
                'content_type' => $uploader->content_type,
                'download_link' => (object)[
                    'web' => route('lloricode.web.uploader.download', $uploader),
                    'api' => route('lloricode.api.uploader.download', $uploader),
                ],
                'readable_size' => formatBytesUnits($uploader->bytes),

                'created_at' => $uploader->created_at->format('F d, Y g:ia'),
                'readable_created_at' => $uploader->created_at->diffForHumans(),
            ]);
        }
        return $return;
    }

    public function uploadFile(UploadedFile $uploadedFile, $label = null) //:Model
    {
        $modelRules = $this->uploaderRules();

        throw_if($modelRules->maxSize < $uploadedFile->getClientSize(), Exception::class, 'Max file size allowed is ' . formatBytesUnits($modelRules->maxSize));


        $pathToSave = Storage::disk($modelRules->disk)->put($this->_storagePath($this), $uploadedFile);

        return $this->uploaders()->create([
            'client_original_name' => $uploadedFile->getClientOriginalName(),
            'label' => $label,
            'user_id' => app()->runningInConsole() ? 1 : auth()->user()->id,
            'content_type' => $uploadedFile->getClientMimeType(),
            'extension' => $uploadedFile->getClientOriginalExtension(),
            'path' => $pathToSave,
            'disk' => $modelRules->disk,
            'bytes' => $uploadedFile->getClientSize(),
        ]);
    }

    private function _storagePath(UploaderContract $model)
    {
        $modelclass = strtolower(get_class($model));

        $modelClassArray = explode('\\', $modelclass);

        // TODO:
        $pathConfig = ''; //config('uploaders.folder_path');

        return Config::get('uploader.implementation', \Lloricode\LaravelUploader\Models\Uploader::class)::PATH_FOLDER . '/' . $pathConfig . $modelClassArray[count($modelClassArray)-1] . '/' . md5($model->id);
    }
}
