<?php
namespace Lloricode\LaravelUploader\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

class LaravelUploaderRouteServiceProvider extends RouteServiceProvider
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
        Route::prefix('api/uploaders')
            ->middleware('api')
            ->as('lloricode.api.uploader.')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../resources/routes/api-route.php');
    }

    protected function mapWebRoutes()
    {
        Route::prefix('uploaders')
            ->middleware('web')
            ->as('lloricode.web.uploader.')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../resources/routes/web-route.php');
    }
}
