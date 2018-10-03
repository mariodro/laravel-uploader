<?php

namespace Lloricode\LaravelUploader\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Lloricode\LaravelUploader\Models\Uploader;
use Illuminate\Database\Schema\Blueprint;
use App\Models\TestModel;
use App\Models\TestPublicModel;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class TestCase extends Orchestra
{
    protected $testModel;
    protected $testPublicModel;
    protected $user;

    public function setUp()
    {
        parent::setUp();
        
        $this->setUpDatabase($this->app);
    }

    public function tearDown()
    {
        $folder = Uploader::PATH_FOLDER .'/';
        Storage::disk('local')->deleteDirectory($folder);
        Storage::disk('public')->deleteDirectory($folder);

        parent::tearDown();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        include_once __DIR__.'/../database/migrations/migration.stub';
        (new \CreateUploadersTable())->up();

        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('test_public_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->timestamps();
        });

        $this->testModel = TestModel::create([
            'name' => 'test',
        ]);

        $this->testPublicModel = TestPublicModel::create([
            'name' => 'test2',
        ]);

        $this->user = User::create([
            'first_name' => 'Basic',
            'last_name' => 'User',
        ]);
    }

    protected function getPackageAliases($app)
    {
        return [
        ];
    }

    protected function getPackageProviders($app)
    {
        return [
            "Lloricode\\LaravelUploader\\Providers\\LaravelUploaderServiceProvider",
            "Lloricode\\LaravelUploader\\Providers\\LaravelUploaderRouteServiceProvider",
        ];
    }
}
