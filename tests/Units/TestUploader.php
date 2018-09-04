<?php

namespace Lloricode\LaravelUploader\Tests\Units;

use Lloricode\LaravelUploader\Tests\TestCase;
use Lloricode\LaravelUploader\Models\Uploader;
use Illuminate\Http\UploadedFile;

class TestUploader extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    public function testAllDefault()
    {
        $fakeFile = UploadedFile::fake()->create('my_file.pdf')->size(123);

        $uploader =  $this->testModel
            ->uploadFile($fakeFile);
            
        $this->assertFileExists(config("filesystems.disks.{$uploader->disk}.root").'/'.$uploader->path);

        $this->assertDatabaseHas((new Uploader)->getTable(), [
            'uploaderable_id' => $this->testModel->id,
            'uploaderable_type' => get_class($this->testModel),
            'client_original_name' => 'my_file.pdf',
            'extension' => 'pdf',
            'disk' => 'local',
            'content_type' => 'application/pdf',
            'user_id' => $this->user->id,
        ]);


        $fakeFile = UploadedFile::fake()->create('my_file_22.pdf')->size(456);

        $uploader =  $this->testModel
            ->uploadFile($fakeFile, 'sample');


        $uploadedFiles =  $this->testModel->getUploadedFiles();
       
        $this->assertCount(2, $uploadedFiles);
    }
}
