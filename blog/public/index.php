<?php

declare(strict_types=1);

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Aura\Router\RouterContainer;
use Core\Http\Router\AuraRouterAdapter;
use Core\Http\Pipeline\MiddlewareResolver;
use Core\Http\Application;
use Core\Http\Middleware\{RouteMiddleware, DispatchMiddleware};
use App\Http\Action\{HomeAction, AboutAction};
use App\Http\Middleware\{NotFoundHandler, ProfilerMiddleware, ErrorHandlerMiddleware};


require dirname(__DIR__)."/vendor/autoload.php";

### Initialization
$params = [
  'debug' => true
];

$aura = new RouterContainer();
$routes = $aura->getMap();

$routes->get('home', '/', new HomeAction());
$routes->get('about', '/about', new AboutAction());

$router = new AuraRouterAdapter($aura);

$resolver = new MiddlewareResolver(new Response());
$app = new Application($resolver, new NotFoundHandler());

$app->pipe(new ErrorHandlerMiddleware($params['debug']));
$app->pipe(ProfilerMiddleware::class);
$app->pipe(new RouteMiddleware($router));

/**
 * @todo потом удалить :)
 * Реализация SRP (SOLID).
 * Разделил RouterMiddleware на 2 класса.
 * Теперь есть возможность вставить промежуточных посредников.
 */
//$app->pipe(\App\Http\Middleware\AboutNotFoundMiddleware::class);

$app->pipe(new DispatchMiddleware($resolver));

### Running
$request = ServerRequestFactory::fromGlobals();
$response = $app->handle($request);

### Sending

$emitter = new SapiEmitter();
$emitter->emit($response);