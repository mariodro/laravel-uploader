<?php

namespace Lloricode\LaravelUploader\Tests\Units;

use Lloricode\LaravelUploader\Tests\TestCase;
use Lloricode\LaravelUploader\Models\Uploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

class TestDelete extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->actingAs($this->user);
    }


    public function testAllDefault()
    {
        $fakeFile = UploadedFile::fake()->create('my_file.pdf')->size(123);
        $ct1 = $fakeFile->getClientMimeType();

        $uploader = $this->testPublicModel
            ->uploadFile($fakeFile);

        $uploadedFiles =  $this->testPublicModel->getUploadedFiles();

        $this->delete($uploadedFiles[0]->download_link->web)
            ->assertStatus(204);


        $this->assertFileNotExists(Config::get("filesystems.disks.{$uploader->disk}.root").'/'.$uploader->path);
    }
}
