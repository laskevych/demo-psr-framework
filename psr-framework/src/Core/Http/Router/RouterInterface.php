<?php

declare(strict_types=1);

namespace Core\Http\Router;

use Core\Http\Router\Exception\RequestNotMatchedException;
use Core\Http\Router\Exception\RouteNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    /**
     * @param ServerRequestInterface $request
     * @throws RequestNotMatchedException
     * @return Result
     */
    public function match(ServerRequestInterface $request): Result;

    /**
     * @param string $name
     * @param array $params
     * @throws RouteNotFoundException
     * @return string
     */
    public function generate(string $name, array $params = []): string;
}