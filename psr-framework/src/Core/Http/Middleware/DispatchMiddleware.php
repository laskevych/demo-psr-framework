<?php

declare(strict_types=1);

namespace Core\Http\Middleware;

use Core\Http\Pipeline\MiddlewareResolver;
use Core\Http\Router\Result;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatchMiddleware implements MiddlewareInterface
{
    private $resolver;

    public function __construct(MiddlewareResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * Получаем $request, а в качестве $handler - NotFoundHandler(404).
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
            return $handler->handle($request);
        }
        $middleware = $this->resolver->resolve($result->getHandler());
        return $middleware->process($request, $handler);
    }
}