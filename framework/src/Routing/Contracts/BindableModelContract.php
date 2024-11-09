<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing\Contracts;

interface BindableModelContract
{
    public static function findForRoute(mixed $value): ?static;
}
