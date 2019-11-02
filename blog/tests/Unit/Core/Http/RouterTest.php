<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Http;

use PHPUnit\Framework\TestCase;
use Zend\Diactoros\{ServerRequest, Uri};
use Core\Http\Router\{Exception\RequestNotMatchedException,
    Exception\RouteNotFoundException,
    RouteCollection,
    SimpleRouter};

class RouterTest extends TestCase
{
    public function testCorrectMethod(): void
    {
        $routes = new RouteCollection();

        $routes->get(
            $nameGet = 'blog_show',
            $path = '/blog/{id}',
            $handlerGet = 'handler_get',
            $tokens = ['id' => '\d+']
        );

        $routes->post(
            $namePost = 'blog_edit',
            $path = '/blog/edit/{id}',
            $handlerPost = 'handler_post'
        );

        $router = new SimpleRouter($routes);

        $result = $router->match($this->buildRequest('GET', '/blog/45'));
        self::assertEquals($nameGet, $result->getName());
        self::assertEquals($handlerGet, $result->getHandler());

        $result = $router->match($this->buildRequest('POST', '/blog/edit/15'));
        self::assertEquals($namePost, $result->getName());
        self::assertEquals($handlerPost, $result->getHandler());
    }

    public function testMissingMethod(): void
    {
        $routes = new RouteCollection();

        $routes->get(
            $nameGet = 'blog',
            $path = '/blog',
            $handlerGet = 'handler_get'
        );

        $router = new SimpleRouter($routes);

        $this->expectException(RequestNotMatchedException::class);

        $router->match($this->buildRequest('DELETE', 'blog'));
    }

    public function testReturnCorrectArgument(): void
    {
        $routes = new RouteCollection();

        $routes->get(
            $nameGet = 'blog_show',
            $path = '/blog/{id}',
            $handlerGet = 'handler_get_show',
            $tokens = ['id' => '\d+']
        );

        $router = new SimpleRouter($routes);

        $result = $router->match($this->buildRequest('GET', '/blog/7'));

        self::assertEquals($nameGet, $result->getName());
        self::assertEquals(['id' => '7'], $result->getAttributes());
    }

    public function testRouteGenerate(): void
    {
        $routes = new RouteCollection();

        $routes->get(
            $nameGet = 'blog_show',
            $path = '/blog/{id}',
            $handlerGet = 'handler_get',
            $tokens = ['id' => '\d+']
        );

        $router = new SimpleRouter($routes);

        $url = $router->generate('blog_show', ['id' => 15]);
        self::assertEquals('/blog/15', $url);

    }

    public function testRouteNotFound(): void
    {
        $routes = new RouteCollection();

        $routes->get(
            $nameGet = 'blog_show',
            $path = '/blog/{id}',
            $handlerGet = 'handler_get',
            $tokens = ['id' => '\d+']
        );

        $router = new SimpleRouter($routes);

        $this->expectException(RouteNotFoundException::class);
        $result = $router->generate('blog', ['id' => 15]);
    }

    private function buildRequest(string $method, string $uri)
    {
        return (new ServerRequest())
            ->withMethod($method)
            ->withUri(new Uri($uri));
    }
}