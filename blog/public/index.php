<?php

declare(strict_types=1);

use Zend\Diactoros\Response\{HtmlResponse, JsonResponse};
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Core\Http\Router\Exception\RequestNotMatchedException;
use App\Http\Action;
use Core\Http\Router\AuraRouterAdapter;
use Core\Http\Pipeline\MiddlewareResolver;
use Aura\Router\RouterContainer;

require dirname(__DIR__)."/vendor/autoload.php";

### Initialization

$aura = new RouterContainer();
$map = $aura->getMap();

$map->get('home', '/', Action\HomeAction::class);
$map->get('about', '/about', Action\AboutAction::class);

$router = new AuraRouterAdapter($aura);
$app = new \Core\Http\Application(new MiddlewareResolver());
$app->pipe(\App\Http\Middleware\ProfilerMiddleware::class);

$request = ServerRequestFactory::fromGlobals();

### Running
try {
    $result = $router->match($request);
    foreach ($result->getAttributes() as $attribute => $value) {
        $request = $request->withAttribute($attribute, $value);
    }
    $handler = $result->getHandler();
    $app->pipe($handler);
} catch (RequestNotMatchedException $e) {}

$response = $app($request, new \App\Http\Middleware\NotFoundHandler());
### Postprocessing

$response = $response->withHeader('X-Developer', 'A. Laskevych');

### Sending

$emitter = new SapiEmitter();
$emitter->emit($response);