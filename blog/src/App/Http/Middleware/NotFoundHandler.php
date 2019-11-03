<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Zend\Diactoros\Response\JsonResponse;

class NotFoundHandler
{
    public function __invoke()
    {
        return new JsonResponse(['error' => 'Undefined page'], 404);
    }
}