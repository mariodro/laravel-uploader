# Laravel Uploader
[![Latest Version on Packagist](https://img.shields.io/packagist/v/lloricode/laravel-uploader.svg)](https://packagist.org/packages/lloricode/laravel-uploader) [![Build Status](https://travis-ci.org/lloricode/laravel-uploader.svg?branch=master)](https://travis-ci.org/lloricode/laravel-uploader) [![Total Downloads](https://img.shields.io/packagist/dt/lloricode/laravel-uploader.svg)](https://packagist.org/packages/lloricode/laravel-uploader)
## Install via [composer](https://getcomposer.org/).
```
composer require lloricode/laravel-uploader
```

## Usage

### Model
```php
<?php

// ....

use Lloricode\LaravelUploader\Contract\UploaderContract; // <--------  add
use Lloricode\LaravelUploader\UploaderOptions; // <--------  add
use Lloricode\LaravelUploader\Traits\UploaderTrait; // <--------  add

class Product extends Model implements UploaderContract // <--------  add
{
    use UploaderTrait; // <--------  add

    public function uploaderRules() :UploaderOptions // <--------  add
    {
        return UploaderOptions::create()
            ->fileNamePrefix('file-')
            ->disk('local') // any disk in config/filesystems.disk
            ->maxSize(20000000); // byte in decimal = 20mb
    }

// ...

```
