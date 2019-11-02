<?php

declare(strict_types=1);

namespace App\Http\Action;

use Zend\Diactoros\Response\JsonResponse;

class AboutAction
{
    public function __invoke()
    {
        return new JsonResponse([
           ['about' => 'Hello!']
        ]);
    }
}