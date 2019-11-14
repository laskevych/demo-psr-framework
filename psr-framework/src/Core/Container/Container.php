<?php

declare(strict_types=1);

namespace Core\Container;

class Container
{
    private $definitions = [];
    private $results = [];

    public function set($name, $value)
    {
        $this->definitions[$name] = $value;
    }

    public function get($name)
    {
        if (array_key_exists($name, $this->results)) {
            return $this->results[$name];
        }
        if (!array_key_exists($name, $this->definitions)) {
            throw new ServiceNotFoundException("Undefined parameter {$name}");
        }

        /**
         * Теперь можем обрабаывать анонимные функции
         */
        $definition = $this->definitions[$name];
        if ($definition instanceof \Closure) {
            $this->results[$name] = $definition($this);
        } else {
            $this->results[$name] = $definition;
        }

        return $this->results[$name];
    }
}