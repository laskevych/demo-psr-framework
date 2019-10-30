<?php

declare(strict_types=1);

namespace App\Tests\Builder\Post;

use App\Model\Post\Id;
use App\Model\Post\Post;
use App\Model\User\User;

class PostBuilder
{
    private $id;
    private $date;

    private $title;
    private $body;
    private $user;
    private $comments;

    public function __construct()
    {
        $this->id = Id::next();
        $this->date = new \DateTimeImmutable();
    }

    public function default(User $user = null, string $title = null, string $body = null): self
    {
        $clone = clone $this;
        $clone->title = $title ?? 'Test Title';
        $clone->body = $body ?? 'Test Body';
        $clone->user = $user ?? new User('Andrew Laskevych');

        return $clone;
    }

    public function build(): Post
    {
        $post = null;

        if (!$this->comments) {
            $post = new Post(
                $this->id,
                $this->date,
                $this->user,
                $this->title,
                $this->body
            );
        }

        if (!$post) {
            throw new \BadMethodCallException('Chose method to create post.');
        }

        return $post;
    }

}