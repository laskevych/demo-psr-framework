<?php

declare(strict_types=1);

namespace App\Model\Comment;

use App\Model\Post\Post;
use App\Model\User\User;

class Comment
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     */
    private $date;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Post
     */
    private $post;

    /**
     * @var string
     */
    private $text;

    public function __construct(Id $id, \DateTimeImmutable $date, User $user, Post $post, string $text)
    {
        $this->id = $id;
        $this->date = $date;
        $this->user = $user;
        $this->post = $post;
        $this->text = $text;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }




}