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
            $data = [
                'client_original_name' => $uploader->client_original_name,
                'label' => $uploader->label,
                'extension' => $uploader->extension,
                'disk' => $uploader->disk,
                'content_type' => $uploader->content_type,
                'download_link' => (object)[
                    'web' => $uploader->downloadLink(),
                    'api' => $uploader->downloadLink('api'),
                ],
                'readable_size' => formatBytesUnits($uploader->bytes),

                'created_at' => $uploader->created_at->format('F d, Y g:ia'),
                'readable_created_at' => $uploader->created_at->diffForHumans(),
                'public_path' => null,
            ];

            // check disk if visibility is public
            $disk = Config::get("filesystems.disks.{$uploader->disk}");
            if (isset($disk['visibility'])) {
                if ($disk['visibility'] == 'public') {
                    $data['public_path'] = $disk['url'] . '/' . $uploader->path;
                }
            }

            $return->push((object)$data);
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
        // TODO:
        $pathConfig = ''; //config('uploaders.folder_path');

        return Config::get('uploader.implementation', \Lloricode\LaravelUploader\Models\Uploader::class)::PATH_FOLDER . '/' .
         $pathConfig . kebab_case(class_basename($model)) . '/' . md5($model->id);
    }
}
