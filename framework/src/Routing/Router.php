<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Exceptions\Routing\RouteNotFoundError;
use DJWeb\Framework\Routing\Contracts\ModelBinderContract;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    /**
     * @var array<int, RouteGroup>
     */
    public private(set) array $groups = [];
    private ModelBinderContract $modelBinder;
    public function __construct(
        private readonly ContainerContract $container,
        public private(set) RouteCollection $routes = new RouteCollection(),
    ) {
        $this->modelBinder = new ModelBinder($this->container);
    }

    public function addRoute(
        Route $route
    ): self {
        $this->routes->addRoute($route);
        return $this;
    }

    public function group(
        string $prefix,
        callable $callback,
        ?string $namespace = null,
        array $middleware = []
    ): void {
        $group = new RouteGroup(
            prefix: $prefix,
            namespace: $namespace,
            middlewareBefore: $middleware
        );

        $callback($group);

        foreach ($group->routes as $route) {
            $this->routes->addRoute($route);
        }

        $this->groups[] = $group;
    }

    /**
     * Dispatch the request to the appropriate handler.
     *
     * @param ServerRequestInterface $request The incoming request
     *
     * @return ResponseInterface The response from the handler
     *
     * @throws RouteNotFoundError If no matching route is found
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $route = $this->routes->findRoute($request);
        $boundParameters = $this->modelBinder->resolveBindings($route);

        $handler = $route->handler;

        return $handler->dispatch($request, $boundParameters, $this->container);
    }
}
