<?php

declare(strict_types=1);

namespace Unit;

use App\Model\Post;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function testSuccess(): void
    {
        $post = new Post(
            $id = 15,
            $date = new \DateTimeImmutable(),
            $title = 'Hello World',
            $body = 'Post Body'
        );

        self::assertEquals($id, $post->getId());
        self::assertEquals($date, $post->getDate());
        self::assertEquals($title, $post->getTitle());
        self::assertEquals($body, $post->getBody());
    }
}