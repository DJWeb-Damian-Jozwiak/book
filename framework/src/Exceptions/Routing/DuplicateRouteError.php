<?php

declare(strict_types=1);

namespace DJWeb\Framework\Exceptions\Routing;

use DJWeb\Framework\Exceptions\InvalidArgumentException;

class DuplicateRouteError extends InvalidArgumentException
{
    public function __construct(string $name)
    {
        parent::__construct("A route with name '{$name}' already exists.");
    }
}
