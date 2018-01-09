<?php

namespace Core;

use GuzzleHttp\Psr7\Response;
use Symfony\Component\Yaml\Yaml;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        $this->config['path'] = dirname(dirname(__DIR__));
    }

    /**
     * Handles the incoming request and returns the appropriate response
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write('coucou');
        return $response;
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
}
