<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Contracts;

interface ValidationRule
{
    public function validate(mixed $value): bool;

    /**
     * @param array<string, mixed> $data
     *
     * @return $this
     */
    public function withData(array $data): static;

}
