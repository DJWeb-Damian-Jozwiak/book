<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class CommandArgument
{
    public function __construct(
        public string $name,
        public string|int|float|null $value = null,
        public string $description = '',
    ) {
    }
}
