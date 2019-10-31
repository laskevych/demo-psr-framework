<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Http;

use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Request;

class RequestTest extends TestCase
{
    public function testSuccess()
    {
        $request = new Request($url = 'http://localhost.com/');

        self::assertEquals($url, $request->getUri());
        self::assertEquals('GET', $request->getMethod());
    }
}