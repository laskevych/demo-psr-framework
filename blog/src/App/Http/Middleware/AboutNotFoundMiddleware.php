<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Core\Http\Router\Result;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class AboutNotFoundMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        /**
         * @var Result $result
         */
        $result = $request->getAttribute(Result::class);
        if ($result && $result->getName() === 'about') {
            return new JsonResponse(['error'=>'about not work']);
        }

        return $next($request);
    }
}