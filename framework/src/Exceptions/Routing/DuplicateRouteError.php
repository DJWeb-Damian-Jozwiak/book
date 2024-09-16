<?php

declare(strict_types=1);

namespace DJWeb\Framework\Exceptions\Routing;

use DJWeb\Framework\Exceptions\InvalidArgument;

class DuplicateRouteError extends InvalidArgument
{
    public function __construct(string $name)
    {
        parent::__construct("A route with name '{$name}' already exists.");
    }
}
