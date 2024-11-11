<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class Route
{
    public string|array $methods;

    /**
     * @param string $path
     * @param string|array<int, string> $methods
     */
    public function __construct(
        public string $path,
        string|array  $methods = ['GET'],
    )
    {
        $this->methods = is_string($methods) ? [$methods] : $methods;
    }

}
