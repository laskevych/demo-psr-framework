<?php

declare(strict_types=1);

namespace Core\Http\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Pipeline
{
    private $queue;

    /**
     * Pipeline constructor
     * Очереди работают по логике FIFO. Первый вошел - первый вышел.
     * И не нужно выдумывать массивы с array_shift() и т.д.
     */
    public function __construct()
    {
        $this->queue = new \SplQueue();
    }


    public function __invoke(ServerRequestInterface $request, callable  $next): ResponseInterface
    {
        // Клонируем очередь, что бы не опустошить ее.
        $delegate = new Next(clone $this->queue, $next);
        return $delegate($request);
    }

    public function pipe($middleware): void
    {
        $this->queue->enqueue($middleware);
    }
}