<?php

namespace Http\Controllers;

use Core\App;
use Twig\Loader\FilesystemLoader;
use Twig\Environment as TwigEnvironment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller {

    protected $twig;

    public function __construct()
    {
        $this->twig = $this->initiateTwig();
    }

    /**
     * Inits Twig environment using the App configuration
     *
     * @return TwigEnvironment
     */
    private function initiateTwig(): TwigEnvironment
    {
        $loader = new FilesystemLoader(App::config('twig', 'templateDir'));
        return new TwigEnvironment($loader, array(
            'cache' => App::config('twig', 'cacheDir'),
            'debug' => App::config('twig', 'debug')
        ));
    }

    /**
     * Renders the given template file with the received arguments, and returns a Response
     *
     * @param string $templateName
     * @param array $parameters
     * @param Request $request
     * @return Response
     */
    protected function render(string $templateName, array $parameters = array(), Request $request = null): Response
    {
        // Retrieve actual HTML code from Twig
        $pageContent = $this->twig->render($templateName, $parameters);

        if ($request === null) {
            $response = new Response();
        }
        $response->setContent($pageContent);

        return $response;
    }
}
