<?php
use Symfony\Component\HttpFoundation\Request;

// Load Composer's autoload
require_once '../vendor/autoload.php';

$app = new Core\App();
$app->boot();

if ($app->config('app', 'debug')) {
    ini_set('display_errors', '-1');
} else {
    ini_set('display_errors', '0');
}

$request = Request::createFromGlobals();
$response = $app->handle($request);
$response->send();

exit;
