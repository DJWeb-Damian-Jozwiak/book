<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use DJWeb\Framework\Exceptions\Routing\DuplicateRouteError;
use DJWeb\Framework\Exceptions\Routing\RouteNotFoundError;
use Psr\Http\Message\RequestInterface;

/**
 * @implements \IteratorAggregate<string, Route>
 */
class RouteCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var list<Route>
     */
    private array $routes = [];

    /**
     * @var array<string, Route>
     */
    private array $namedRoutes = [];

    /**
     * Add a route to the collection.
     *
     * @param Route $route The route to add
     *
     * @throws DuplicateRouteError If a route with the same name already exists
     */
    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;

        if ($route->name) {
            $this->namedRoutes[$route->name] = $route;
        }
    }

    /**
     * Find a route that matches the given request.
     *
     * @param RequestInterface $request The request to match against
     *
     * @return Route|null The matching route, or null if no match is found
     */
    public function findRoute(RequestInterface $request): Route
    {
        $matcher = new RouteMatcher();
        $matchingRoutes = array_filter(
            $this->routes,
            static fn (Route $route) => $matcher->matches($request, $route)
        );
        return array_values($matchingRoutes)[0] ?? throw new RouteNotFoundError(
            'No route found for ' . $request->getMethod(
            ) . ' ' . $request->getUri()->getPath()
        );
    }

    /**
     * Get a route by its name.
     *
     * @param string $name The name of the route
     *
     * @return Route|null The named route, or null if not found
     */
    public function getNamedRoute(string $name): ?Route
    {
        return $this->namedRoutes[$name] ?? null;
    }

    /**
     * Get all routes in the collection.
     *
     * @return array<Route>
     */
    public function getRoutes(): array
    {
        return array_values($this->routes);
    }

    /**
     * @return \Traversable<Route>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     * Get the number of routes in the collection.
     *
     * @return int The number of routes
     */
    public function count(): int
    {
        return count($this->routes);
    }
}
