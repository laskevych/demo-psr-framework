<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Core\Http\Router\Result;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class AboutNotFoundMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @var Result $result
         */
        $result = $request->getAttribute(Result::class);
        if ($result && $result->getName() === 'about') {
            return new JsonResponse(['error'=>'about not work']);
        }

        return $handler->handle($request);
    }
}