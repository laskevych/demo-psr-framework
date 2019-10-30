<?php

declare(strict_types=1);

namespace App\Model\Post;

use App\Model\User\User;

class Post
{
    /**
     * @var Id
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
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $body;


    public function __construct(Id $id, \DateTimeImmutable $date, User $user, string $title, string $body)
    {
        $this->id = $id;
        $this->date = $date;
        $this->user = $user;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string|null
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->user;
    }


}