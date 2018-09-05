<?php
namespace Lloricode\LaravelUploader\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelUploaderServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $config = realpath(__DIR__).'/../../config/uploader.php';

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $config => base_path('config/uploader.php'),
            ]);

            if (! preg_match('/lumen/i', app()->version())) {
                if (! class_exists('CreateUploadersTable')) {
                    $timestamp = date('Y_m_d_His', time());
                    $this->publishes([
                        __DIR__.'/../../database/migrations/migration.stub' => $this->app->databasePath()."/migrations/{$timestamp}_create_uploaders_table.php",
                    ], 'migrations');
                }
            }
        }

        $this->mergeConfigFrom($config, 'uploader');
    }

    /**
    * Register the application services.
    *
    * @return void
    */
    public function register()
    {
    }
}
