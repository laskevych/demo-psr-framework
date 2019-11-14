<?php

declare(strict_types=1);

namespace Core\Http\Pipeline;

class UnknownMiddlewareTypeException extends \InvalidArgumentException
{
    private $type;

    public function __construct($type)
    {
        parent::__construct('Unknown middleware type');
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }
}