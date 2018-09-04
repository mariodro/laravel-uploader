<?php

namespace Lloricode\LaravelUploader\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lloricode\LaravelUploader\Models\Uploader as Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Lloricode\LaravelUploader\Contract\UploaderContract;
use Exception;

trait UploaderTrait
{

    /**
     * Return Uploader relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function uploaders() :MorphMany
    {
        return $this->morphMany(Model::class, 'uploaderable');
    }

    public function delete()
    {
        foreach ($this->uploaders as $uploader) {
            
            // delete file
            $uploader->delete();
        }

        parent::delete();
    }

    public function uploadFile(UploadedFile $uploadedFile, $label = null)
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

        return Model::PATH_FOLDER . '/' . $pathConfig . $modelClassArray[count($modelClassArray)-1] . '/' . md5($model->id);
    }
}
