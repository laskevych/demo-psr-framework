<?php

declare(strict_types=1);

use Zend\Diactoros\Response\{HtmlResponse, JsonResponse};
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Core\Http\Router\Exception\RequestNotMatchedException;
use App\Http\Action;
use Core\Http\Router\AuraRouterAdapter;
use Core\Http\ActionResolver;
use Aura\Router\RouterContainer;

require dirname(__DIR__)."/vendor/autoload.php";

### Initialization

$aura = new RouterContainer();
$map = $aura->getMap();

$map->get('home', '/', Action\HomeAction::class);
$map->get('about', '/about', Action\AboutAction::class);

$router = new AuraRouterAdapter($aura);
$resolver = new ActionResolver();

$request = ServerRequestFactory::fromGlobals();

### Running
try {
    $result = $router->match($request);
    foreach ($result->getAttributes() as $attribute => $value) {
        $request = $request->withAttribute($attribute, $value);
    }
    $handler = $result->getHandler();
    $action = $resolver->resolve($handler);
    $response = $action($request);
} catch (RequestNotMatchedException $e) {
    $response = new JsonResponse(['error' => 'Undefined page'], 404);
}

### Postprocessing

$response = $response->withHeader('X-Developer', 'A. Laskevych');

### Sending

$emitter = new SapiEmitter();
$emitter->emit($response);