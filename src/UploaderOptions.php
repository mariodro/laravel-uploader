<?php

namespace Lloricode\LaravelUploader;

use Exception;
use Illuminate\Support\Facades\Config;

class UploaderOptions
{
    public $maxSize;
    public $disk;
    public $fileNamePrefix;
    public $enableDeleteFile;


    public static function create(): self
    {
        return (new static())->reset();
    }

    public function reset(): self
    {
        $this->fileNamePrefix = 'file';
        $this->disk = Config::get('filesystems.default');
        $this->maxSize = 20000000;  // 20 mb in byte decimal
        $this->enableDeleteFile = false;

        return $this;
    }

    public function fileNamePrefix(string $fileNamePrefix): self
    {
        $this->fileNamePrefix = $fileNamePrefix;
        return $this;
    }

    /**
     * Must be existed in Config filesystems's disk
     *
     * @param boolean $disk
     * @return self
     */
    public function disk(string $disk): self
    {
        $disks = array_keys(Config::get('filesystems.disks'));

        throw_if(!in_array($disk, $disks), Exception::class, 'Invalid storage parameter in ' . get_class($this) . '->disk($disk)');

        $this->disk = $disk;
        return $this;
    }
    
    public function maxSize(int $maxSize): self
    {
        $this->maxSize = $maxSize;
        return $this;
    }

    public function enableDeleteFile(bool $enableDeleteFile): self
    {
        $this->enableDeleteFile = $enableDeleteFile;
        return $this;
    }
}
