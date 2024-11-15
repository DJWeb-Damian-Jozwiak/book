<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing\Contracts;

use DJWeb\Framework\Routing\Route;

interface ModelBinderContract
{
    /**
     * @param Route $route
     *
     * @return array<string, mixed>
     */
    public function resolveBindings(Route $route): array;
}
