<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Http;

use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;

class ResponseTest extends TestCase
{
    public function testSuccess(): void
    {
        $response = new HtmlResponse($body = 'Body');

        self::assertEquals($body, $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('OK', $response->getReasonPhrase());
    }

    public function testNotFound(): void
    {
        $response = new HtmlResponse($body = 'Hello!', 404);

        self::assertEquals($body, $response->getBody()->getContents());
        self::assertEquals(strlen($body), $response->getBody()->getSize());
        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals('Not Found', $response->getReasonPhrase());
    }

    public function testHeaders(): void
    {
        $response = (new HtmlResponse($body = 'Hello!', 200))
            ->withHeader($name1 = 'X-Test-Header-1', $value1 = 'Hello 1')
            ->withHeader($name2 = 'X-Test-Header-2', $value2 = 'Hello 2');

        //var_dump($response->getHeaders(), $response->getHeader($name1), $response->getHeaderLine($name2));
        //self::assertTrue(true);

        self::assertEquals($value1, $response->getHeaderLine($name1));
        self::assertEquals($value2, $response->getHeaderLine($name2));
        self::assertArrayHasKey($name1, $response->getHeaders());
        self::assertArrayHasKey($name2, $response->getHeaders());
    }
}