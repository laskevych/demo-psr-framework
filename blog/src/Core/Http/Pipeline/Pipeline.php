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

    public function pipe(callable $middleware): void
    {
        $this->queue->enqueue($middleware);
    }

    public function __invoke(ServerRequestInterface $request, callable  $default): ResponseInterface
    {
        return $this->next($request, $default);
    }

    private function next(ServerRequestInterface $request, callable $default)
    {
        if (!$current = $this->queue->isEmpty()) {
            return $default($request);
        }

        $current = $this->queue->dequeue();

        return $current($request, function (ServerRequestInterface $request) use ($default) {
            return $this->next($request, $default);
        });
    }
}