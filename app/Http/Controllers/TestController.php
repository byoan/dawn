<?php

namespace Http\Controllers;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller {

    public function homeAction(Request $request, string $slug, string $slug2): Response
    {
        return $this->render('test.twig', array(
            'test' => 'test'
        ));
    }

    public function testAction(): Response
    {
        return new Response('test');
    }
}
