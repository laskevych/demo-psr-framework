<?php

declare(strict_types=1);

namespace App\Model;


class Post
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
     * @var string
     */
    private $title;

    /**
     * @var string|null
     */
    private $body;

    public function __construct(int $id, \DateTimeImmutable $date, string $title, ?string $body)
    {
        $this->id = $id;
        $this->date = $date;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getId(): int
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
    public function getBody(): ?string
    {
        return $this->body;
    }
}