<?php

declare(strict_types=1);

namespace App\Core\Http;

class ActionResolver
{
    public function resolse($handler): callable
    {
        return \is_string($handler) ? new $handler() : $handler;
    }
}