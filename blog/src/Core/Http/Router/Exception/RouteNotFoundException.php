<?php

declare(strict_types=1);

namespace Core\Http\Router\Exception;

class RouteNotFoundException extends \LogicException
{
    private $name;
    private $params;

    public function __construct(string $name, array $params, \Throwable $previous = null)
    {
        parent::__construct("Route {$name} not found.", 0, $previous);
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}