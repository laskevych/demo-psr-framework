<?php

declare(strict_types=1);

use Zend\Diactoros\Response\{HtmlResponse, JsonResponse};
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Core\Http\Router\RouteCollection;
use Psr\Http\Message\ServerRequestInterface;
use Core\Http\Router\Router;
use Core\Http\Router\Exception\RequestNotMatchedException;
use App\Http\Action;

require dirname(__DIR__)."/vendor/autoload.php";

### Initialization

$aura = new \Aura\Router\RouterContainer();
$map = $aura->getMap();

$map->get('home', '/', Action\HomeAction::class);

//$routes->get('index_blog', '/blog', function () {
//    return new JsonResponse([
//        ['id' => 1, 'title' => 'The First Post'],
//        ['id' => 2, 'title' => 'The Second Post'],
//    ]);
//}, ['id' => '\d+', 'title' => '']);
//
//$routes->get('show_blog', '/blog/{id}', function (ServerRequestInterface $request) {
//    $id = $request->getAttribute('id');
//
//    return new JsonResponse([
//        ['id' => $id, 'title' => "Post #$id"]
//    ]);
//}, ['id' => '\d+']);

$request = ServerRequestFactory::fromGlobals();

### Running
try {

    $matcher = $aura->getMatcher();

    $result = $matcher->match($request);
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