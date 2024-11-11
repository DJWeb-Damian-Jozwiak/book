<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use DJWeb\Framework\DBAL\Models\Model;

readonly class RouteBinding
{
    public function __construct(
        public string $modelClass,
        public string $findMethod = 'findForRoute',
        public ?\Closure $condition = null,
    ) {
    }

    public function validCondition(?object $model): bool
    {
        if(! $this->condition) {
            return true;
        }
        return (bool) ($this->condition)($model);
    }
}
