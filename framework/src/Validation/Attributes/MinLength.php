<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MinLength extends ValidationAttribute
{
    public function __construct(
        public int $minLength,
        ?string $message = null
    )
    {
        $this->message = $message ?? "The field must be at least {$this->minLength} characters long.";
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function validate(mixed $value, array $data = []): bool
    {
        return strlen($value) >= $this->minLength;
    }
}
