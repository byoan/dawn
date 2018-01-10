<?php

namespace Core;

use Core\Routing\Router;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class App {

    /**
     * The application configuration
     *
     * @var array
     */
    public static $config;

    /**
     * Boots the application
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadAppConfig();
        $this->defineBasePaths();
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
     * Handles the incoming request and returns the appropriate response
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        // Initiate our router, which will pass the request to the Symfony router
        $router = new Router($request, self::$config['app']['basePath']);
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
}
