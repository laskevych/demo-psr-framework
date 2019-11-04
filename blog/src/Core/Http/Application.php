<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Http\Pipeline\MiddlewareResolver;
use Core\Http\Pipeline\Pipeline;

class Application extends Pipeline
{
    private $resolver;

    public function __construct(MiddlewareResolver $resolver)
    {
        parent::__construct();
        $this->resolver = $resolver;
    }

    public function pipe($middleware): void
    {
        parent::pipe($this->resolver->resolve($middleware));
    }
}