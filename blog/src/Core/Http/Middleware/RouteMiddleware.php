<?php

declare(strict_types=1);

namespace Core\Http\Middleware;

use Core\Http\Router\Exception\RequestNotMatchedException;
use Core\Http\Router\Result;
use Core\Http\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;

class RouteMiddleware
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke(ServerRequestInterface $request, callable  $next)
    {
        try {
            $result = $this->router->match($request);
            foreach ($result->getAttributes() as $attribute => $value) {
                $request = $request->withAttribute($attribute, $value);
            }

            return $next($request->withAttribute(Result::class, $result));
        } catch (RequestNotMatchedException $e) {
            return $next($request);
        }
    }
}