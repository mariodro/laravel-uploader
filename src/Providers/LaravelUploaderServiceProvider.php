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
        if (! preg_match('/lumen/i', app()->version())) {
            if (! class_exists('CreateUploadersTable')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/migration.stub' => $this->app->databasePath()."/migrations/{$timestamp}_create_uploaders_table.php",
                ], 'migrations');
            }
        }
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
