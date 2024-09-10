<?php

namespace DJWeb\Framework\Routing;

use DJWeb\Framework\Container\Contracts\ContainerInterface;
use DJWeb\Framework\Exceptions\Routing\RouteNotFoundException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

readonly class Router
{
    public function __construct(
        private ContainerInterface $container,
        private RouteCollection $routes = new RouteCollection()
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
    public function addRoute(string $method, string $path, callable|array $handler, ?string $name = null): self
    {
        $route = new Route($path, $method, $handler, $name);
        $this->routes->addRoute($route);
        return $this;
    }

    /**
     * Dispatch the request to the appropriate handler.
     *
     * @param RequestInterface $request The incoming request
     * @return ResponseInterface The response from the handler
     * @throws RouteNotFoundException If no matching route is found
     */
    public function dispatch(RequestInterface $request): ResponseInterface
    {
        $route = $this->routes->findRoute($request);

        if ($route === null) {
            throw new RouteNotFoundException(
                'No route found for ' . $request->getMethod() . ' ' . $request->getUri()->getPath()
            );
        }

        $handler = $route->getHandler();

        if (is_array($handler)) {
            [$controllerName, $method] = $handler;
            $controller = $this->container->get($controllerName);
            $handler = [$controller, $method];
        }
        /** @phpstan-ignore-next-line */
        return $handler($request);
    }
}