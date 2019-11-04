<?php

declare(strict_types=1);

namespace Core\Http\Middleware;

use Core\Http\Pipeline\MiddlewareResolver;
use Core\Http\Router\Result;
use Psr\Http\Message\ServerRequestInterface;

class DispatchMiddleware
{
    private $resolver;

    public function __construct(MiddlewareResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        /**
         * Получаем $request, а в качестве $next - NotFoundHandler(404).
         * Если мы его поставим последним в цепочке вызовов.
         * Если не определен Result::class в $result
         * Мы передаем все в NotFoundHandler и получим 404
         * Иначе берем handler и передаем все дальше
         *
         * Теперь между определении маршрута и между выполнянием самого Action
         * мы можем вставить нужные нам проверочные middleware @example index.php
         * По сути это реализация SRP (SOLID).
         * @var Result $result
         */
        if (!$result = $request->getAttribute(Result::class)) {
            return $next($request);
        }
        $middleware = $this->resolver->resolve($result->getHandler());
        return $middleware($request, $next);
    }
}