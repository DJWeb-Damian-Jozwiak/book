<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use DJWeb\Framework\Routing\Attributes\Middleware;
use DJWeb\Framework\Routing\Attributes\Route as RouteAttribute;
use DJWeb\Framework\Routing\Attributes\RouteGroup as RouteGroupAttribute;
use DJWeb\Framework\Routing\Attributes\RouteParam;
use ReflectionClass;
use ReflectionMethod;

class RegisterControllerRoutes
{
    /**
     * @param Controller $controller
     *
     * @return RouteGroup|array<int, Route>
     */
    public function register(Controller $controller): RouteGroup|array
    {
        $reflection = new ReflectionClass($controller);
        /** @var ?\ReflectionAttribute<RouteGroupAttribute> $attribute */
        $attribute = $reflection->getAttributes(RouteGroupAttribute::class)[0] ?? null;
        $groupAttribute = $attribute?->newInstance() ?? null;
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methods = array_filter(
            $methods,
            static fn ($method) => count($method->getAttributes(RouteAttribute::class)) > 0
        );
        $routes = array_map($this->mapMethod(...), $methods);
        if ($groupAttribute) {
            $group = new RouteGroup($groupAttribute->name);
            foreach ($routes as $route) {
                $group->addRoute($route);
            }
            return $group;
        }
        return $routes;
    }

    private function mapMethod(ReflectionMethod $method): Route
    {
        $routeAttribute = $method->getAttributes(RouteAttribute::class)[0]?->newInstance();
        $handler = new RouteHandler($method->class, $method->name);
        $route = new Route($routeAttribute->path, $routeAttribute->methods[0], $handler);
        $middleware = $method->getAttributes(Middleware::class)[0] ?? null;
        $middleware = $middleware?->newInstance();
        $middleware?->addToRoute($route);
        $params = $method->getAttributes(RouteParam::class);
        $params = array_filter($params, static fn ($param) => $param->newInstance()->bind !== null, );
        foreach ($params as $param) {
            /** @var RouteParam $instance */
            $instance = $param->newInstance();
            $route = $route->bind($instance->name ?? '', $instance->bind ?? '');
        }
        return $route;
    }
}
