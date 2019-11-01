<?php

declare(strict_types=1);

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

require dirname(__DIR__)."/vendor/autoload.php";

### Initialization

$request = ServerRequestFactory::fromGlobals();

$path = $request->getUri()->getPath();

if ($path == '/') {
    $name = $request->getQueryParams()['name'] ?? 'Guest';
    $response = (new HtmlResponse('Hello, '. $name . '!'));
} elseif ($path === '/about') {
    $response = new HtmlResponse('Simple About');
} else {
    $response = new \Zend\Diactoros\Response\JsonResponse(['error' => 'Undefined page'], 404);
}

### Preprocessing
// TODO:
//if (preg_match('/json/i', $request->getHeaderLine('Content-Type'))) {
//    $request = $request->withParsedBody(json_decode($request->getBody()->getContents()));
//}

### Action



### Postprocessing

$response = $response->withHeader('X-Test', 'Hello');

### Sending

$emitter = new SapiEmitter();
$emitter->emit($response);