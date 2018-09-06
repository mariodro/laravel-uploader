<?php

namespace Lloricode\LaravelUploader\Tests\Units;

use Lloricode\LaravelUploader\Tests\TestCase;
use Lloricode\LaravelUploader\Models\Uploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

class TestUploaderPublic extends TestCase
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

        $uploader =  $this->testPublicModel
            ->uploadFile($fakeFile);
            
        $this->assertFileExists(Config::get("filesystems.disks.{$uploader->disk}.root").'/'.$uploader->path);

        $this->assertDatabaseHas((new Uploader)->getTable(), [
            'uploaderable_id' => $this->testPublicModel->id,
            'uploaderable_type' => get_class($this->testPublicModel),
            'client_original_name' => 'my_file.pdf',
            'extension' => 'pdf',
            'disk' => 'public',
            'content_type' => $ct1,
            'user_id' => $this->user->id,
        ]);
        

        $fakeFile = UploadedFile::fake()->create('my_file_22.pdf')->size(456);
        // $ct2 = $fakeFile->getClientMimeType();

        $uploader2 =  $this->testPublicModel
            ->uploadFile($fakeFile, 'sample');

        $this->assertFileExists(Config::get("filesystems.disks.{$uploader2->disk}.root").'/'.$uploader2->path);


        $uploadedFiles =  $this->testPublicModel->getUploadedFiles();

        $this->assertCount(2, $uploadedFiles);

        $this->assertNotNull($uploadedFiles[0]->public_path);

        $this->assertEquals('my_file.pdf', $uploadedFiles[0]->client_original_name);
        $this->assertNull($uploadedFiles[0]->label);
        $this->assertEquals('pdf', $uploadedFiles[0]->extension);
        $this->assertEquals($ct1, $uploadedFiles[0]->content_type);

        $this->assertEquals('http://localhost/api/uploaders/1', $uploadedFiles[0]->download_link->api);
        $this->assertEquals('http://localhost/uploaders/1', $uploadedFiles[0]->download_link->web);

        $this->get($uploadedFiles[0]->download_link->web)
            ->assertStatus(200);

        $this->actingAs($this->user, 'api');
        $this->get($uploadedFiles[0]->download_link->api)
                ->assertStatus(200);

        $this->testPublicModel->delete();

        $this->assertFileNotExists(Config::get("filesystems.disks.{$uploader->disk}.root").'/'.$uploader->path);
        $this->assertFileNotExists(Config::get("filesystems.disks.{$uploader2->disk}.root").'/'.$uploader2->path);
    }
}
