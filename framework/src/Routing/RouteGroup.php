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

    /**
     * @param string $prefix
     * @param string|null $namespace
     * @param array<int, string> $middlewareBefore
     * @param array<int, string> $middlewareAfter
     */
    public function __construct(
        string $prefix = '',
        public readonly ?string $namespace = null,
        public readonly array $middlewareBefore = [],
        public readonly array $middlewareAfter = []
    ) {
        $this->prefix = $this->normalizePath($prefix);
    }

    /**
     * @param string $prefix
     * @param callable $callback
     * @param string|null $namespace
     * @param array<int, string> $middleware
     *
     * @return void
     */
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
        $before = $this->middlewareBefore;
        $after = $this->middlewareAfter;
        array_walk($before, static fn (string $middleware) => $route->withMiddlewareBefore($middleware));
        array_walk($after, static fn (string $middleware) => $route->withMiddlewareAfter($middleware));
        $this->routes[] = $route;
    }

    private function normalizePath(string $path): string
    {
        // Usuń trailing slash
        $path = rtrim($path, '/');

        // Zamień multiple slashes na pojedynczy
        $path = preg_replace('#/+#', '/', $path);
        $path ??= '';

        // Dodaj leading slash jeśli nie ma
        if (! str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        return $path;
    }
}
