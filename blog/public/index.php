<?php

declare(strict_types=1);

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

require dirname(__DIR__)."/vendor/autoload.php";

### Initialization

$request = ServerRequestFactory::fromGlobals();

### Action

$name = $request->getQueryParams()['name'] ?? 'Guest';

$response = (new HtmlResponse('Hello, '. $name . '!'))
    ->withHeader('X-Test', 'Hello');

### Sending

$emitter = new SapiEmitter();
$emitter->emit($response);