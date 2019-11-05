<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Container;

use Core\Container\Container;
use PHPUnit\Framework\TestCase;

class TestContainer extends TestCase
{
    public function testSimpleFunction(): void
    {
        $container = new Container();

        $container->set($name = 'name', $value = 'value');
        self::assertEquals($value, $container->get($name));

        $container->set($name = 'name', $value = 26);
        self::assertEquals($value, $container->get($name));

        $container->set($name = 'name', $value = ['array']);
        self::assertEquals($value, $container->get($name));

        $container->set($name = 'name', $value = new \stdClass());
        self::assertEquals($value, $container->get($name));
    }

    public function testNotFound(): void
    {
        $container = new Container();

        $this->expectException(\InvalidArgumentException::class);

        $container->get('test');
    }
}