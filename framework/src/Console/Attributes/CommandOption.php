<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class CommandOption
{
    public function __construct(
        public string $name,
        public bool|string|int|float|null|\Closure $value = null,
        public bool $default = true,
        public bool $required = false,
        public string $description = '',
    ) {
    }
}
