<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use InvalidArgumentException;
use Psr\Http\Server\MiddlewareInterface;

class Route
{
    /**
     * @var array<int, string>
     */
    public private(set) array $middlewareBefore = [];
    /**
     * @var array<int, string>
     */
    public private(set) array $middlewareAfter = [];
    /**
     * @var array<int, string>
     */
    public private(set) array $withoutMiddleware = [];

    /**
     * @var array<string, RouteParameter>
     */
    public private(set) array $parameterDefinitions = [];

    /**
     * @var array<string, mixed>
     */
    public private(set) array $parameters = [];

    /**
     * @var array<int|string, RouteBinding>
     */
    public private(set) array $bindings = [];

    private readonly string $method;

    public function __construct(
        public string $path,
        string $method,
        public readonly RouteHandler $handler,
        public readonly ?string $name = null
    ) {
        $this->method = strtoupper($method);
        $this->parseParameters();
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return $this
     */
    public function withParameters(array $parameters): static
    {
       $this->parameters = $parameters;
        return $this;
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

    public function bind(
        string $parameter,
        string $model,
        string $findMethod = 'findForRoute',
    ): self {
        $this->bindings[$parameter] = new RouteBinding(
            modelClass: $model,
            findMethod: $findMethod,
        );

        return $this;
    }

    private function verifyMiddleware(string $middleware): void
    {
        if (! is_subclass_of($middleware, MiddlewareInterface::class)) {
            throw new InvalidArgumentException("{$middleware} must implement MiddlewareInterface");
        }
    }

    private function parseParameters(): void
    {
        preg_match_all('/<([^>]+)>/', $this->path, $matches);

        foreach ($matches[1] as $param) {
            $parts = explode(':', $param);
            $name = $parts[0];
            $pattern = $parts[1] ?? '[^/]+';

            $this->parameterDefinitions[$name] = new RouteParameter(
                name: $name,
                pattern: $pattern,
            );
        }
    }
}
