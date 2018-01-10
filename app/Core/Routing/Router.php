<?php

namespace Core\Routing;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KervelEvents;
use Symfony\Component\Routing\Router as SfRouter;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;

class Router implements HttpKernelInterface {

    private $context;

    private $request;

    private $sfRouter;

    private $resolver;

    private $argumentResolver;

    public function __construct(Request $request, string $basePath)
    {
        $this->request = $request;
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);
        $this->resolver = new ControllerResolver();
        $this->argumentResolver = new ArgumentResolver();

        $locator = new FileLocator($basePath);
        $loader = new YamlFileLoader($locator);
        $loader->load('config/routes.yml');

        $this->sfRouter = new SfRouter(
            $loader,
            'config/routes.yml',
            array(),
            $this->context
        );
    }

    /**
     * Handles a request
     *
     * @param Request $request
     * @param int $type
     * @param boolean $catch
     * @return Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        // Create an event from the request
        $event = new GetResponseEvent($this, $request, $type);

        // Match the request using the SF router
        $parameters = $this->sfRouter->matchRequest($request);

        // Assign found attributes to the request object
        $request->attributes->add(array('_controller' => $parameters['_controller']));
        $request->attributes->add(array('_route' => $parameters['_route']));
        unset($parameters['_controller'], $parameters['_route']);

        // Assign request parameters to the attributes field used by the resolver
        foreach ($parameters as $name => $value) {
            $request->attributes->add(array($name => $value));
        }

        // Load the controller that matches the request
        if (false === $controller = $this->resolver->getController($request)) {
            throw new NotFoundHttpException(sprintf('Unable to find the controller for path "%s". The route is wrongly configured.', $request->getPathInfo()));
        }

        $event = new FilterControllerEvent($this, $controller, $request, $type);
        $controller = $event->getController();

        // Retrieve the controller arguments
        $arguments = $this->argumentResolver->getArguments($request, $controller);

        $event = new FilterControllerArgumentsEvent($this, $controller, $arguments, $request, $type);
        $controller = $event->getController();
        $arguments = $event->getArguments();

        // Make the actual call to the controller, sending him the args
        $response = call_user_func_array($controller, $arguments);

        // Check that we return a proper Response object
        if (!$response instanceof Response) {
            $event = new GetResponseForControllerResultEvent($this, $request, $type, $response);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            }

            if (!$response instanceof Response) {
                $msg = sprintf('The controller must return a response (%s given).', $this->varToString($response));

                // the user may have forgotten to return something
                if (null === $response) {
                    $msg .= ' Did you forget to add a return statement somewhere in your controller?';
                }
                throw new \LogicException($msg);
            }
        }

        return $response;
    }
}
