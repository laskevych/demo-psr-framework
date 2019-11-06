<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Container;

use Core\Container\Container;
use Core\Container\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
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

    public function testCallback(): void
    {
        $container = new Container();

        $container->set($name = 'call_back', function () {
            return new \stdClass();
        });

        self::assertNotNull($value = $container->get($name));
        self::assertInstanceOf(\stdClass::class, $value);
    }

    public function testNotFound(): void
    {
        $container = new Container();

        $this->expectException(ServiceNotFoundException::class);

        $container->get('test');
    }
}