<?php

namespace Core\Providers;

use Core\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class RoutingServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRequestContext();
        $this->registerControllerResolver();
        $this->registerArgumentResolver();

        $this->registerRouter();
    }

    /**
     * Registers our Router
     *
     * @return void
     */
    private function registerRouter(): void
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app);
        });
    }

    /**
     * Registers the Symfony Request Context class
     *
     * @return void
     */
    private function registerRequestContext(): void
    {
        $this->app->singleton('requestContext', function () {
            return new RequestContext();
        });
    }

    /**
     * Register the Symfony Argument Resolver
     *
     * @return void
     */
    private function registerArgumentResolver(): void
    {
        $this->app->singleton('argumentResolver', function () {
            return new ArgumentResolver();
        });

    }

    /**
     * Registers the Symfony Controller Resolver
     *
     * @return void
     */
    private function registerControllerResolver(): void
    {
        $this->app->singleton('controllerResolver', function () {
            return new ControllerResolver();
        });
    }

}
