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

//$map->get('profiler', '/profiler', function (\Psr\Http\Message\ServerRequestInterface $request) {
//   $profiler = new \App\Http\Middleware\ProfilerMiddleware();
//   $about = new Action\AboutAction();
//
//   return $profiler($request, $about);
//});

$router = new AuraRouterAdapter($aura);
$resolver = new MiddlewareResolver();
$pipeline = new \Core\Http\Pipeline\Pipeline();
$pipeline->pipe($resolver->resolve(\App\Http\Middleware\ProfilerMiddleware::class));

$request = ServerRequestFactory::fromGlobals();

### Running
try {
    $result = $router->match($request);
    foreach ($result->getAttributes() as $attribute => $value) {
        $request = $request->withAttribute($attribute, $value);
    }
    $handlers = $result->getHandler();
    foreach (is_array($handlers) ? $handlers : [$handlers] as $handler) {
        $pipeline->pipe($resolver->resolve($handler));
    }
} catch (RequestNotMatchedException $e) {}

$response = $pipeline($request, new \App\Http\Middleware\NotFoundHandler());
### Postprocessing

$response = $response->withHeader('X-Developer', 'A. Laskevych');

### Sending

$emitter = new SapiEmitter();
$emitter->emit($response);