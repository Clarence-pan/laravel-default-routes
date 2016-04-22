<?php


namespace Clarence\LaravelDefaultRoutes;


use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class DefaultRouteProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $router->any('/{controller}/{action?}', DefaultRouteController::class.'@runControllerAction');
        $router->any('/{module}/{controller}/{action?}', DefaultRouteController::class.'@runModuleControllerAction');
    }

    public function register()
    {

    }

}