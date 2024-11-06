<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Exceptions\Routing\RouteNotFoundError;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class Router
{
    public function __construct(
        private ContainerContract $container,
        public private(set) RouteCollection $routes = new RouteCollection()
    ) {
    }

    /**
     * Add a new route to the collection.
     *
     * @param string $method The HTTP method
     * @param string $path The URL path
     * @param callable|array<int, string> $handler The route handler
     * @param string|null $name Optional route name
     */
    public function addRoute(
        Route $route
    ): self {
        $this->routes->addRoute($route);
        return $this;
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

        $handler = $route->handler;

        if (is_array($handler)) {
            [$controllerName, $method] = $handler;
            $controller = $this->container->get($controllerName);
            $handler = [$controller, $method];
        }
        /** @phpstan-ignore-next-line */
        return $handler($request);
    }
}
