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
    private $config;

    /**
     * Boots the application
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadAppConfig();
        $this->defineBasePath();
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

                if (!empty($configFile)) {
                    $this->config = $config;
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
    private function defineBasePath(): void
    {
        $this->config['base_path'] = dirname(dirname(__DIR__));
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
        $router = new Router($request, $this->config['base_path']);
        return $router->handle($request);
    }

    /**
     * Sends the received response
     *
     * @param ResponseInterface $response
     * @return void
     */
    public function sendResponse(ResponseInterface $response) {
        \Http\Response\send($response);
    }

    /**
     * Returns the configuration value for the specified key
     * TODO : replace with a Configuration Provider ? Replace exception with custom one
     * @param string $value
     * @return void
     */
    public function config(string $domain, string $key)
    {
        if (isset($this->config[$domain][$key])) {
            return $this->config[$domain][$key];
        } else {
            throw new \Exception('Undefined configuration key');
        }
    }
}
