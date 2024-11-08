<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use DJWeb\Framework\Enums\MiddlewarePosition;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class Route
{
    /**
     * @var array<int, class-string<MiddlewareInterface>>
     */
    public private(set) array $middlewareBefore = [];
    /**
     * @var array<int, class-string<MiddlewareInterface>>
     */
    public private(set) array $middlewareAfter = [];
    /**
     * @var array<int, class-string<MiddlewareInterface>>
     */
    public private(set) array $withoutMiddleware = [];


    /**
     * @var callable|array<int, string>
     */
    public private(set) mixed $handler;
    private readonly string $method;

    /**
     * @param string $path
     * @param string $method
     * @param callable|array<int, string> $handler
     * @param string|null $name
     */
    public function __construct(
        public readonly string $path,
        string $method,
        callable|array $handler,
        public readonly ?string $name = null
    ) {
        $this->method = strtoupper($method);
        $this->handler = $handler;
    }

    public function withMiddlewareBefore(string $middleware): static
    {
        $this->verifyMiddleware($middleware);
        $this->middlewareBefore [] = $middleware;
        return $this;
    }

    public function withMiddlewareAfter(string $middleware): static
    {
        $this->verifyMiddleware($middleware);
        $this->middlewareAfter [] = $middleware;
        return $this;
    }

    public function withoutMiddleware(string $middleware): static
    {
        $this->verifyMiddleware($middleware);
        $this->withoutMiddleware [] = $middleware;
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function execute(RequestInterface $request): ResponseInterface
    {
        /** @phpstan-ignore-next-line */
        return call_user_func($this->handler, $request);
    }

    private function verifyMiddleware(string $middleware): void
    {
        if (! is_subclass_of($middleware, MiddlewareInterface::class)) {
            throw new InvalidArgumentException("{$middleware} must implement MiddlewareInterface");
        }
    }
}
