<?php

namespace DJWeb\Framework\Exceptions\Routing;

use DJWeb\Framework\Exceptions\InvalidArgumentException;

class DuplicateRouteException extends InvalidArgumentException
{
    public function __construct(string $name)
    {
        parent::__construct("A route with name '$name' already exists.");
    }
}