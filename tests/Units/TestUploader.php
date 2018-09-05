<?php

namespace Lloricode\LaravelUploader\Tests\Units;

use Lloricode\LaravelUploader\Tests\TestCase;
use Lloricode\LaravelUploader\Models\Uploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

class TestUploader extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    private function _getTestFileUploded($name = 'my_file.pdf')
    {
        return new UploadedFile(__DIR__ . '/../files/test.pdf', $name);
    }

    public function testAllDefault()
    {
        $fakeFile = $this->_getTestFileUploded();
        $ct1 = $fakeFile->getClientMimeType();

        $uploader =  $this->testModel
            ->uploadFile($fakeFile);
            
        $this->assertFileExists(Config::get("filesystems.disks.{$uploader->disk}.root").'/'.$uploader->path);

        $this->assertDatabaseHas((new Uploader)->getTable(), [
            'uploaderable_id' => $this->testModel->id,
            'uploaderable_type' => get_class($this->testModel),
            'client_original_name' => 'my_file.pdf',
            'extension' => 'pdf',
            'disk' => 'local',
            'content_type' => $ct1,
            'user_id' => $this->user->id,
        ]);
        // UploadedFile::fake()->create('my_file_22.pdf')->size(456)

        $fakeFile = $this->_getTestFileUploded('my_file_22.pdf');
        // $ct2 = $fakeFile->getClientMimeType();

        $uploader =  $this->testModel
            ->uploadFile($fakeFile, 'sample');


        $uploadedFiles =  $this->testModel->getUploadedFiles();

        $this->assertCount(2, $uploadedFiles);

        $this->assertEquals('my_file.pdf', $uploadedFiles[0]->client_original_name);
        $this->assertNotNull($uploadedFiles[0]->path);
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
    }
}
