<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Contracts;

interface ValidationRule
{
    /**
     * @param mixed $value
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function validate(mixed $value, array $data = []): bool;

    public string $message {
        get;
    }
}
