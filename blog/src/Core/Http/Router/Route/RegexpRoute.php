<?php

declare(strict_types=1);

namespace App\Core\Http\Router\Route;

use App\Core\Http\Router\Result;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

class RegexpRoute implements RouteInterface
{

    private $name;
    private $pattern;
    private $handler;
    private $methods;
    private $tokens;

    public function __construct(string $name, string $pattern, $handler, array $methods, array $tokens)
    {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->methods = $methods;
        $this->tokens = $tokens;
    }

    /**
     * GRASP - Information Expert - все данные для роутинга у нас тут, потому методы для проверок пишем тут а не в классе Router
     * Route сам решает подходит ли он для выполнения задачи
     * Проходит все машруты проверяет если метод совпадает, то идем дальше.
     * Дальше на основе шаблона мы создаем регулярное выражение и дальше парсим урл по этому выражению.
     * В роутах мы пишем blog/{id}/{slug}, а он все перепарсивает.
     */
    public function match(ServerRequestInterface $request): ?Result
    {

        if ($this->methods && !in_array($request->getMethod(), $this->methods, true)) {
            return null;
        }

        $pattern = preg_replace_callback('~\{([^\}]+)\}~', function ($matches) {
            $argument = $matches[1]; //id
            $replace = $this->tokens[$argument] ?? '[^}]+';
            return '(?P<' . $argument . '>' . $replace . ')';
        }, $this->pattern);


        if (preg_match('~^'. $pattern. '$~i', $request->getUri()->getPath(), $matches)) {
            return new Result(
                $this->name,
                $this->handler,
                array_filter($matches, '\is_string', ARRAY_FILTER_USE_KEY)
            );
        }

        return null;
    }

    /**
     * GRASP - Information Expert - все данные для роутинга у нас тут, потому методы для проверок пишем тут а не в классе Router
     * Route сам решает подходит ли он для выполнения задачи
     */
    public function generate(string $name, array $params = []): ?string
    {
        $arguments = array_filter($params);

        if ($this->name !== $name) {
            return null;
        }

        $url = preg_replace_callback('~\{([^\}]+)\}~', function ($matches) use (&$arguments) {
            $argument = $matches[1]; //id
            if (!array_key_exists($argument, $arguments)) {
                throw new \InvalidArgumentException('Missing parameter "'. $argument . '"');
            }

            return $arguments[$argument];
        }, $this->pattern);

        if ($url !== null) {
            return $url;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }
}