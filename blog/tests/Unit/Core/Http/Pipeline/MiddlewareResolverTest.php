<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Http\Pipeline;

use Core\Http\Pipeline\MiddlewareResolver;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequest;

class MiddlewareResolverTest extends TestCase
{
    /**
     * @dataProvider getValidHandlers
     * @param $handler
     */
    public function testDirect($handler): void
    {
        $resolver = new MiddlewareResolver(new Response());
        $middleware = $resolver->resolve($handler);

        /** @var ResponseInterface $response */
        $response = $middleware->process(
            (new ServerRequest())->withAttribute('attribute', $value = 'value'),
            new NotFoundHandler()
        );

        self::assertEquals([$value], $response->getHeader('X-Header'));

    }

    /**
     * @dataProvider getValidHandlers
     * @param $handler
     */
    public function testNext($handler): void
    {
        $resolver = new MiddlewareResolver(new Response());
        $middleware = $resolver->resolve($handler);

        /** @var ResponseInterface $response */
        $response = $middleware->process(
            (new ServerRequest())->withAttribute('next', true),
            new NotFoundMiddleware()
        );

        self::assertEquals(404, $response->getStatusCode());
    }

    public function testArray()
    {
        $resolver = new MiddlewareResolver(new Response());

        $middleware = $resolver->resolve([
            new DummyMiddleware(),
            new SinglePassMiddleware()
        ]);

        /** @var ResponseInterface $response */
        $response = $middleware->process(
            (new ServerRequest())->withAttribute('attribute', $value = 'value'),
            new NotFoundHandler()
        );

        self::assertEquals(['dummy'], $response->getHeader('X-Dummy'));
        self::assertEquals([$value], $response->getHeader('X-Header'));
    }

    public function getValidHandlers()
    {
        return [
            'SinglePass Callback' => [function (ServerRequestInterface $request, callable $next) {
                if ($request->getAttribute('next')) {
                    return $next($request);
                }
                return (new HtmlResponse(''))
                    ->withHeader('X-Header', $request->getAttribute('attribute'));
            }],
            'SinglePass Class' => [SinglePassMiddleware::class],
            'SinglePass Object' => [new SinglePassMiddleware()],
            'DoublePass Callable' => [function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
                if ($request->getAttribute('next')) {
                    return $next($request, $response);
                }
                return $response
                    ->withHeader('X-Header', $request->getAttribute('attribute'));
            }],
            'DoublePass Class' => [DoublePassMiddleware::class],
            'DoublePass Object' => [new DoublePassMiddleware()],
            'Psr Middleware Class' => [PsrMiddleware::class],
            'Psr Middleware Object' => [new PsrMiddleware()],
            'Psr Handler Class' => [PsrHandler::class],
            'Psr Handler Object' => [new PsrHandler()],
        ];
    }
}

/**
 * Обычный callable
 * Class SinglePassMiddleware
 * @package Tests\Unit\Core\Http\Pipeline
 */
class SinglePassMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        if ($request->getAttribute('next')) {
            return $next($request);
        }
        return (new HtmlResponse(''))
            ->withHeader('X-Header', $request->getAttribute('attribute'));
    }
}

/**
 * DoublePass - когда передаем request, response и next.
 * Можно брать из заголовки response
 * Class DoublePassMiddleware
 * @package Tests\Unit\Core\Http\Pipeline
 */
class DoublePassMiddleware
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        if ($request->getAttribute('next')) {
            return $next($request, $response);
        }
        return $response
            ->withHeader('X-Header', $request->getAttribute('attribute'));
    }
}

/**
 * Реализует стандарты PSR-15
 * Class PsrMiddleware
 * @package Tests\Unit\Core\Http\Pipeline
 */
class PsrMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getAttribute('next')) {
            return $handler->handle($request);
        }
        return (new HtmlResponse(''))
            ->withHeader('X-Header', $request->getAttribute('attribute'));
    }
}

class PsrHandler implements RequestHandlerInterface
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return (new HtmlResponse(''))
            ->withHeader('X-Header', $request->getAttribute('attribute'));
    }
}

class NotFoundHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(404);
    }
}

class DummyMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        return $next($request)
            ->withHeader('X-Dummy', 'dummy');
    }
}