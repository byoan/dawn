<?php

namespace Core\Providers;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;


class YamlFileLoaderServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('yamlFileLoader', function ($app) {
            $locator = new FileLocator($app::config('app', 'basePath'));
            return new YamlFileLoader($locator);
        });
    }
}
