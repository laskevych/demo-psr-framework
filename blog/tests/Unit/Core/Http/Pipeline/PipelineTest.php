<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Http\Pipeline;

use Core\Http\Pipeline\MiddlewareResolver;
use Core\Http\Pipeline\Pipeline;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequest;

class PipelineTest extends TestCase
{
    public function testPipe(): void
    {
        $pipeline = new Pipeline();
        $resolver = new MiddlewareResolver();

        $pipeline->pipe($resolver->resolve(Middleware1::class));
        $pipeline->pipe($resolver->resolve(Middleware2::class));

        $response = $pipeline(new ServerRequest(), new Last());

        $this->assertJsonStringEqualsJsonString(
            json_encode(['middleware-1' => 1, 'middleware-2' => 2]),
            $response->getBody()->getContents()
        );
    }
}

class Middleware1
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        return $next($request->withAttribute('middleware-1', 1));
    }
}

class Middleware2
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        return $next($request->withAttribute('middleware-2', 2));
    }
}

class Last
{
    public function __invoke(ServerRequestInterface $request)
    {
        return new JsonResponse($request->getAttributes());
    }
}