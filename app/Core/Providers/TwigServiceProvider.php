<?php

namespace Core\Providers;

use Illuminate\Support\ServiceProvider;
use Twig\Loader\FilesystemLoader;
use Twig\Environment as TwigEnvironment;

class TwigServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('twig', function ($app) {
            $loader = new FilesystemLoader($app::config('twig', 'templateDir'));
            return new TwigEnvironment($loader, array(
                'cache' => $app::config('twig', 'cacheDir'),
                'debug' => $app::config('twig', 'debug')
            ));
        });
    }
}
