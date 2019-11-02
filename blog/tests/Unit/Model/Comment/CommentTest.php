<?php


namespace Tests\Unit\Model\Comment;

use App\Model\Comment\{Id, Comment};
use App\Model\User\User;
use Tests\Builder\Post\PostBuilder;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testSuccess(): void
    {
        $post = (new PostBuilder())->default()->build();

        $comment = new Comment(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $user = new User('John Smith'),
            $post,
            $text = 'My first comment.'
        );

        self::assertEquals($id, $comment->getId());
        self::assertEquals($date, $comment->getDate());
        self::assertEquals($user->getValue(), $comment->getUser()->getValue());
        self::assertEquals($post->getId(), $comment->getPost()->getId());
        self::assertEquals($text, $comment->getText());
    }
}