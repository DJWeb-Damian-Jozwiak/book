<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Utils;

readonly class CommandNamespace
{
    public function __construct(
        public string $namespace,
        public string $path
    ) {
    }
}
