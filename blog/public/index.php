<?php

declare(strict_types=1);

use Zend\Diactoros\Response\{HtmlResponse, JsonResponse};
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Core\Http\Router\Exception\RequestNotMatchedException;
use App\Http\Action\{HomeAction, AboutAction};
use Core\Http\Router\AuraRouterAdapter;
use Core\Http\Pipeline\MiddlewareResolver;
use Aura\Router\RouterContainer;
use Core\Http\Application;
use App\Http\Middleware\{NotFoundHandler, ProfilerMiddleware, ErrorHandlerMiddleware};
use Psr\Http\Message\ServerRequestInterface;

require dirname(__DIR__)."/vendor/autoload.php";

### Initialization
$params = [
  'debug' => true
];

$aura = new RouterContainer();
$map = $aura->getMap();

$map->get('home', '/', HomeAction::class);
$map->get('about', '/about', AboutAction::class);

$router = new AuraRouterAdapter($aura);
$app = new Application(new MiddlewareResolver(), new NotFoundHandler());
$request = ServerRequestFactory::fromGlobals();

$app->pipe(new ErrorHandlerMiddleware($params['debug']));

$app->pipe(function (ServerRequestInterface $request, callable $next) {
    /**
     * @var \Psr\Http\Message\ResponseInterface $response
     */
    $response = $next($request);
    return $response->withHeader('X-Developer', 'A. Laskevych');
});

$app->pipe(ProfilerMiddleware::class);

### Running
try {
    $result = $router->match($request);
    foreach ($result->getAttributes() as $attribute => $value) {
        $request = $request->withAttribute($attribute, $value);
    }
    $handler = $result->getHandler();
    $app->pipe($handler);
} catch (RequestNotMatchedException $e) {}

$response = $app->run($request);


### Sending

$emitter = new SapiEmitter();
$emitter->emit($response);