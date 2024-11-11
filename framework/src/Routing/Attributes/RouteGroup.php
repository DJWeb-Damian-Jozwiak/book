<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class RouteGroup
{
    public function __construct(public string $name)
    {
    }
}
