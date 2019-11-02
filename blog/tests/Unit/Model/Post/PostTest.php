<?php

declare(strict_types=1);

namespace Tests\Builder\Post;

use App\Model\Post\{Id, Post};
use App\Model\User\User;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function testSuccess(): void
    {
        $post = new Post(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $user = new User('Andrew Laskevych'),
            $title = 'Hello World',
            $body = 'Post Body'
        );

        self::assertEquals($id, $post->getId());
        self::assertEquals($date, $post->getDate());
        self::assertEquals($user->getValue(), $post->getAuthor()->getValue());
        self::assertEquals($title, $post->getTitle());
        self::assertEquals($body, $post->getBody());
    }
}