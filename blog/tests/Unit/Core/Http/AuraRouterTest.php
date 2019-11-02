<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Http;

use Aura\Router\RouterContainer;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\{ServerRequest, Uri};
use Core\Http\Router\{AuraRouterAdapter, Exception\RequestNotMatchedException, Exception\RouteNotFoundException};

class RouterTest extends TestCase
{
    public function testCorrectMethod(): void
    {
        $aura = new RouterContainer();
        $map = $aura->getMap();

        $map->get(
            $nameGet = 'blog_show',
            $path = '/blog/{id}',
            $handlerGet = 'handler_get'
        )->tokens($tokens = ['id' => '\d+']);

        $map->post(
            $namePost = 'blog_edit',
            $path = '/blog/edit/{id}',
            $handlerPost = 'handler_post'
        );

        $router = new AuraRouterAdapter($aura);

        $result = $router->match($this->buildRequest('GET', '/blog/45'));
        self::assertEquals($nameGet, $result->getName());
        self::assertEquals($handlerGet, $result->getHandler());

        $result = $router->match($this->buildRequest('POST', '/blog/edit/15'));
        self::assertEquals($namePost, $result->getName());
        self::assertEquals($handlerPost, $result->getHandler());
    }

    public function testMissingMethod(): void
    {
        $aura = new RouterContainer();
        $map = $aura->getMap();

        $map->get(
            $nameGet = 'blog',
            $path = '/blog',
            $handlerGet = 'handler_get'
        );

        $router = new AuraRouterAdapter($aura);

        $this->expectException(RequestNotMatchedException::class);

        $router->match($this->buildRequest('DELETE', 'blog'));
    }

    public function testReturnCorrectArgument(): void
    {
        $aura = new RouterContainer();
        $map = $aura->getMap();

        $map->get(
            $nameGet = 'blog_show',
            $path = '/blog/{id}',
            $handlerGet = 'handler_get_show'
        )->tokens(['id' => '\d+']);

        $router = new AuraRouterAdapter($aura);

        $result = $router->match($this->buildRequest('GET', '/blog/7'));

        self::assertEquals($nameGet, $result->getName());
        self::assertEquals(['id' => '7'], $result->getAttributes());
    }

    public function testRouteGenerate(): void
    {
        $aura = new RouterContainer();
        $map = $aura->getMap();

        $map->get(
            $nameGet = 'blog_show',
            $path = '/blog/{id}',
            $handlerGet = 'handler_get'
        )->tokens(['id' => '\d+']);

        $router = new AuraRouterAdapter($aura);

        $url = $router->generate('blog_show', ['id' => 15]);
        self::assertEquals('/blog/15', $url);

    }

    public function testRouteNotFound(): void
    {
        $aura = new RouterContainer();
        $map = $aura->getMap();

        $map->get(
            $nameGet = 'blog_show',
            $path = '/blog/{id}',
            $handlerGet = 'handler_get'
        )->tokens(['id' => '\d+']);

        $router = new AuraRouterAdapter($aura);

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