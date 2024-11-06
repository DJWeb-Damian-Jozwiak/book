<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use DJWeb\Framework\Config\Config;

class MiddlewareConfig
{
    public function configure(Kernel $kernel): void
    {
        $router = $kernel->router;
        $withoutMiddleware = $router->routes->withoutMiddleware;
        $middleware = Config::get('middleware');
        $items = $this->mapMiddleware($kernel, $middleware);
        $kernel->withMiddleware($items);
        $kernel->withoutMiddleware($withoutMiddleware);
    }

    public function mapMiddleware(Kernel $kernel, mixed $middleware): array
    {
        $router = $kernel->router;
        $middlewareWithBefore = $router->routes->middlewareBefore;
        $middlewareWithAfter = $router->routes->middlewareAfter;
        return array_map(
            static fn (string $middlewareClass) => $kernel->container->get($middlewareClass),
            [
                ...$middleware['before_global'] ?? [],
                ...$middlewareWithBefore,
                ...$middleware['global'] ?? [],
                ...$middleware['after_global'] ?? [],
                ...$middlewareWithAfter,
            ]
        );
    }
}
