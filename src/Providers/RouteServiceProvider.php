<?php
namespace Lloricode\LaravelUploader\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

class LaravelUploaderServiceProvider extends RouteServiceProvider
{
    protected $namespace ='Lloricode\LaravelUploader\Http\Controllers';

    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }


    protected function mapApiRoutes()
    {
    }

    protected function mapWebRoutes()
    {
        Route::prefix('uploaders')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../web-route.php');
    }
}
