<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing\Attributes;

use Attribute;
use DJWeb\Framework\Routing\Route as RouteRouting;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class Middleware
{
    /**
     * @param array<int, string> $beforeGlobal
     * @param array<int, string> $afterGlobal
     * @param array<int, string> $withoutMiddleware
     */
    public function __construct(
        private array $beforeGlobal = [],
        private array $afterGlobal = [],
        private array $withoutMiddleware = []
    )
    {
    }

    public function addToRoute(RouteRouting $route): void
    {
        array_walk($this->beforeGlobal, $route->withMiddlewareBefore(...));
        array_walk($this->afterGlobal, $route->withMiddlewareAfter(...));
        array_walk($this->withoutMiddleware, $route->withoutMiddleware(...));
    }
}
