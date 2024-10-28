<?php

declare(strict_types=1);

namespace DJWeb\Framework\Log\Formatters;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Log\Contracts\FormatterContract;

abstract readonly class Formatter implements FormatterContract
{
    public function __construct(protected ContainerContract $container)
    {
    }
}
