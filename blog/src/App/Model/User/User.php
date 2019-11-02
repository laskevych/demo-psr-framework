<?php

declare(strict_types=1);

namespace App\Model\User;

use Webmozart\Assert\Assert;

class User
{
    private $value;

    public function __construct(string $value)
    {
        Assert::lengthBetween($value, 1, 32);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}