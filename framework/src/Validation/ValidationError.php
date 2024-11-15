<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation;

readonly class ValidationError
{
    public function __construct(
        public string $field,
        public string $message
    ) {
    }
}
