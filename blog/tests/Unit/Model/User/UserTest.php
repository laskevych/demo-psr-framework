<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User;

use App\Model\User\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSuccess():void
    {
        $user = new User($name = 'John Smith');

        self::assertEquals($name, $user->getValue());
    }


    public function testLength(): void
    {
        $this->expectException('InvalidArgumentException');

        $user = new User($name = '');
        $user = new User($name = 'John Smith John Smith John Smith John Smith ');
    }
}