<?php
use Symfony\Component\HttpFoundation\Request;

// Load Composer's autoload
require_once '../vendor/autoload.php';

$app = new Core\App();
$app->boot();

$request = Request::createFromGlobals();
$response = $app->handle($request);
$response->send();

exit;
