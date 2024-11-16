<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Attributes;

use DJWeb\Framework\Validation\Contracts\ValidationRule;

abstract class ValidationAttribute implements ValidationRule
{
    public protected(set) string $message;
    /**
     * @var array<string, mixed>
     */
    protected private(set) array $data;

    /**
     * @param array<string, mixed> $data
     *
     * @return $this
     */
    public function withData(array $data): static
    {
        $this->data = $data;
        return $this;
    }
}
