<?php

declare(strict_types=1);

namespace App\Http\Action;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

class HomeAction
{
    public function __invoke(ServerRequestInterface $request)
    {
        throw new \RuntimeException('Joke!', 500);
        $name = $request->getQueryParams()['name'] ?? 'Guest';
        return new HtmlResponse('Hello, '. $name . '!');
    }
}