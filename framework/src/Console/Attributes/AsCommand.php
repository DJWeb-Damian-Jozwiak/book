<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class AsCommand
{
    public function __construct(
        public string $name
    ) {
    }
}
