<?php

namespace Http\Controllers;

use Core\App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Container\Container;

class Controller
{
    protected $container;

    public function __construct()
    {
        $this->container = Container::getInstance();
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
        $pageContent = $this->container->get('twig')->render($templateName, $parameters);

        if ($request === null) {
            $response = new Response();
        }
        $response->setContent($pageContent);

        return $response;
    }
}
