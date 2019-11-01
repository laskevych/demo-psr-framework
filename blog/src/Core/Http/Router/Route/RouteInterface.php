<?php

declare(strict_types=1);

namespace App\Core\Http\Router\Route;

use App\Core\Http\Router\Result;
use Psr\Http\Message\ServerRequestInterface;

interface RouteInterface
{
    public function match(ServerRequestInterface $request): ?Result;

    public function generate(string $name, array $params = []): ?string;
}