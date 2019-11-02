<?php

declare(strict_types=1);

namespace Core\Http\Router;

use Psr\Http\Message\ServerRequestInterface;
use Core\Http\Router\Exception\{RequestNotMatchedException, RouteNotFoundException};

class Router
{
    private $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    public function match(ServerRequestInterface $request): Result
    {
        foreach ($this->routes->getRoutes() as $route) {
            if ($result = $route->match($request)) {
                return $result;
            }
        }

        throw new RequestNotMatchedException($request);
    }

    /**
     * Проходимс по маршрутам. Ищем по имени.
     * Дальше по ругулярке заменяем все переданные значения и фомируем урл.
     * @param string $name
     * @param array $params
     * @return string
     */
    public function generate(string $name, array $params = []): string
    {
        foreach ($this->routes->getRoutes() as $route) {
            if (null !== $url = $route->generate($name, $params)) {
                return $url;
            }
        }

        throw new RouteNotFoundException($name, $params);
    }
}