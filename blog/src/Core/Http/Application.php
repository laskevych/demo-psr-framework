<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Http\Pipeline\MiddlewareResolver;
use Core\Http\Pipeline\Pipeline;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application extends Pipeline
{
    private $resolver;
    private $defaultHandler;

    public function __construct(MiddlewareResolver $resolver, callable $defaultHandler)
    {
        parent::__construct();
        $this->resolver = $resolver;
        $this->defaultHandler = $defaultHandler;
    }

    public function pipe($middleware): void
    {
        parent::pipe($this->resolver->resolve($middleware));
    }

    public function run(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this($request, $response, $this->defaultHandler);
    }
}