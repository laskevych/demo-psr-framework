<?php

declare(strict_types=1);

namespace Core\Http\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Next
{
    private $queue;
    private $next;
    private $response;

    public function __construct(\SplQueue $queue, ResponseInterface $response, callable $next)
    {
        $this->queue = $queue;
        $this->response = $response;
        $this->next = $next;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        /*
         * Вызываем все middleware из очереди.
         * Передаем Request и элемент Next
         * Если Next не будет, то прилетит заглушка по умолчанию.
         */
        if ($this->queue->isEmpty()) {
            return ($this->next)($request);
        }

        $middleware = $this->queue->dequeue();

        return $middleware($request, $this->response, function (ServerRequestInterface $request) {
            return $this($request);
        });
    }
}