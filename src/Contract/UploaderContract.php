<?php

namespace Lloricode\LaravelUploader\Contract;

use Lloricode\LaravelUploader\UploaderOptions;

interface UploaderContract
{
    public function uploaderRules() :UploaderOptions;
}
