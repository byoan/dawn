<?php

namespace Core;

use Core\Routing\Router;
use Illuminate\Support\Arr;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Container\Container;
use Core\Providers\LogServiceProvider;
use Illuminate\Support\ServiceProvider;
use Core\Providers\RoutingServiceProvider;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Core\Providers\YamlFileLoaderServiceProvider;

class App extends Container {

    /**
     * The application configuration
     *
     * @var array
     */
    public static $config;

    /**
     * Indicates if the application has "booted".
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * All of the registered service providers.
     *
     * @var array
     */
    protected $serviceProviders = [];

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = [];

    /**
     * Boots the application
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadAppConfig();
        $this->defineBasePaths();
        $this->registerAppBindings();

        $this->booted = true;

        $this->registerCoreProviders();
    }

    /**
     * Loads the app configuration
     *
     * @return void
     */
    private function loadAppConfig(): void
    {
        $basePath = dirname(dirname(__DIR__));
        if (file_exists($basePath . '/config/app.yml')) {
            try {
                $config = Yaml::parseFile($basePath . '/config/app.yml');

                if (!empty($config)) {
                    self::$config = $config;
                }
            } catch (Exception $e) {
                // Log exception
            }
        }
    }

    /**
     * Defines the base path of the application
     *
     * @return void
     */
    private function defineBasePaths(): void
    {
        self::$config['app']['basePath'] = dirname(dirname(__DIR__));
        self::$config['twig']['templateDir'] = self::$config['app']['basePath'] . self::$config['twig']['templateDir'];
        self::$config['twig']['cacheDir'] = self::$config['app']['basePath'] . self::$config['twig']['cacheDir'];
    }

    /**
     * Registers the Application basic bindings within the container
     *
     * @return void
     */
    private function registerAppBindings(): void
    {
        static::setInstance($this);

        $this->instance('app', $this);
        $this->instance(Container::class, $this);
    }

    /**
     * Registers the core providers used by the Application
     *
     * @return void
     */
    private function registerCoreProviders(): void
    {
        $this->register(new LogServiceProvider($this));
        $this->register(new RoutingServiceProvider($this));
        $this->register(new YamlFileLoaderServiceProvider($this));
    }

    /**
     * Handles the incoming request and returns the appropriate response
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        // Initiate our router, which will pass the request to the Symfony router
        $router = new Router($this);
        return $router->handle($request);
    }

    /**
     * Returns the configuration value for the specified key
     * TODO : replace with a Configuration Provider ? Replace exception with custom one
     * @param string $value
     * @return void
     */
    public static function config(string $domain, string $key)
    {
        if (isset(self::$config[$domain][$key])) {
            return self::$config[$domain][$key];
        } else {
            throw new \Exception('Undefined configuration key');
        }
    }

    /**
     * Register a service provider with the application.
     *
     * @param  \Illuminate\Support\ServiceProvider|string  $provider
     * @param  array  $options
     * @param  bool   $force
     * @return \Illuminate\Support\ServiceProvider
     */
    public function register($provider, $options = [], $force = false)
    {
        if (($registered = $this->getProvider($provider)) && ! $force) {
            return $registered;
        }
        // If the given "provider" is a string, we will resolve it, passing in the
        // application instance automatically for the developer. This is simply
        // a more convenient way of specifying your service provider classes.
        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        if (method_exists($provider, 'register')) {
            $provider->register();
        }

        $this->markAsRegistered($provider);

        // If the application has already booted, we will call this boot method on
        // the provider class so it has an opportunity to do its boot logic and
        // will be ready for any usage by this developer's application logic.
        if ($this->booted) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    /**
     * Boot the given service provider.
     *
     * @param  \Illuminate\Support\ServiceProvider  $provider
     * @return mixed
     */
    protected function bootProvider(ServiceProvider $provider)
    {
        if (method_exists($provider, 'boot')) {
            return $this->call([$provider, 'boot']);
        }
    }

    /**
     * Get the registered service provider instance if it exists.
     *
     * @param  \Illuminate\Support\ServiceProvider|string  $provider
     * @return \Illuminate\Support\ServiceProvider|null
     */
    public function getProvider($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        return Arr::first($this->serviceProviders, function ($value) use ($name) {
            return $value instanceof $name;
        });
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param  string  $provider
     * @return \Illuminate\Support\ServiceProvider
     */
    public function resolveProvider($provider)
    {
        return new $provider($this);
    }

    /**
     * Mark the given provider as registered.
     *
     * @param  \Illuminate\Support\ServiceProvider  $provider
     * @return void
     */
    protected function markAsRegistered($provider)
    {
        $this->serviceProviders[] = $provider;

        $this->loadedProviders[get_class($provider)] = true;
    }
}
