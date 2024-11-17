<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

readonly class RouteBinding
{
    public function __construct(
        public string $modelClass,
        public string $findMethod = 'findForRoute',
    ) {
    }
}
