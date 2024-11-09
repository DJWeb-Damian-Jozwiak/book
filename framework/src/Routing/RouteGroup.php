<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

class RouteGroup
{
    /**
     * @var array<int, Route>
     */
    public private(set) array $routes = [];

    public readonly string $prefix;

    public function __construct(
        string $prefix = '',
        public readonly ?string $namespace = null,
        public readonly array $middlewareBefore = [],
        public readonly array $middlewareAfter = []
    ) {
        $this->prefix = $this->normalizePath($prefix);
    }

    public function group(
        string $prefix,
        callable $callback,
        ?string $namespace = null,
        array $middleware = []
    ): void {
        $fullPrefix = $this->prefix . '/' . ltrim($prefix, '/');
        $namespace = $namespace ?? $this->namespace;

        $group = new RouteGroup(
            prefix: $fullPrefix,
            namespace: $namespace,
            middlewareBefore: [...$this->middlewareBefore, ...$middleware]
        );

        $callback($group);

        foreach ($group->routes as $route) {
            $this->routes[] = $route;
        }
    }

    public function addRoute(Route $route): void
    {
        $route->path = $this->prefix . '/' . ltrim($route->path, '/');
        if ($this->namespace) {
            $route = new Route(
                path: $route->path,
                method: $route->getMethod(),
                handler: $route->handler->withNamespace($this->namespace),
                name: $route->name,
            );
        }
        array_walk(
            $this->middlewareBefore,
            static fn (string $middleware) => $route->withMiddlewareBefore($middleware)
        );
        array_walk(
            $this->middlewareAfter,
            static fn (string $middleware) => $route->withMiddlewareAfter($middleware)
        );
        $this->routes[] = $route;
    }

    private function normalizePath(string $path): string
    {
        // Usuń trailing slash
        $path = rtrim($path, '/');

        // Zamień multiple slashes na pojedynczy
        $path = preg_replace('#/+#', '/', $path);

        // Dodaj leading slash jeśli nie ma
        if (! str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        return $path;
    }
}
