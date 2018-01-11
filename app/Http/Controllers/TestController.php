<?php

namespace Http\Controllers;

use Entities\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    public function homeAction(Request $request, int $userId): Response
    {
        $user = new User($userId);
        return $this->render('test.twig', array(
            'test' => 'test',
            'user' => $user
        ));
    }

    public function testAction(): Response
    {
        return new Response('test');
    }
}
