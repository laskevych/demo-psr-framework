<?php

declare(strict_types=1);

use Zend\Diactoros\Response\{HtmlResponse, JsonResponse};
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use App\Core\Http\Router\RouteCollection;
use Psr\Http\Message\ServerRequestInterface;
use App\Core\Http\Router\Router;
use App\Core\Http\Router\Exception\RequestNotMatchedException;

require dirname(__DIR__)."/vendor/autoload.php";

### Initialization

$routes = new RouteCollection();
$resolver = new \App\Core\Http\ActionResolver();

$routes->get('home', '/', function (ServerRequestInterface $request) {
   $name = $request->getQueryParams()['name'] ?? 'Guest';
   return new HtmlResponse('Hello, '. $name . '!');
});

$routes->get('index_blog', '/blog', function () {
    return new JsonResponse([
        ['id' => 1, 'title' => 'The First Post'],
        ['id' => 2, 'title' => 'The Second Post'],
    ]);
}, ['id' => '\d+', 'title' => '']);

$routes->get('show_blog', '/blog/{id}', function (ServerRequestInterface $request) {
    $id = $request->getAttribute('id');

    return new JsonResponse([
        ['id' => $id, 'title' => "Post #$id"]
    ]);
}, ['id' => '\d+']);

$route = new Router($routes);
//var_dump($route->getRoutes());
//die();
$request = ServerRequestFactory::fromGlobals();

### Running
try {
    $result = $route->match($request);
    foreach ($result->getAttributes() as $attribute => $value) {
        $request = $request->withAttribute($attribute, $value);
    }
    $handler = $result->getHandler();
    $action = $resolver->resolse($handler);
    $response = $action($request);
} catch (RequestNotMatchedException $e) {
    $response = new JsonResponse(['error' => 'Undefined page'], 404);
}

### Postprocessing

$response = $response->withHeader('X-Developer', 'A. Laskevych');

### Sending

$emitter = new SapiEmitter();
$emitter->emit($response);