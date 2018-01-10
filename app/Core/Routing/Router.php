<?php

namespace Core\Routing;

class Router implements HttpKernelInterface {

    private $context;

    private $request;

    private $sfRouter;
    public function __construct(Request $request, string $basePath)
    {
        $this->request = $request;
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);

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
