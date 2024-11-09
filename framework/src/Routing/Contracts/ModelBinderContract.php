<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing\Contracts;

use DJWeb\Framework\Routing\Route;

interface ModelBinderContract
{
    public function resolveBindings(Route $route): array;
}
