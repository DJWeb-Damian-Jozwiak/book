<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MaxLength extends ValidationAttribute
{
    public function __construct(
        public int $maxLength,
        ?string $message = null
    )
    {
        $this->message = $message ?? "The field must be at most {$this->maxLength} characters long.";
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function validate(mixed $value, array $data = []): bool
    {
        return strlen($value) <= $this->maxLength;
    }
}
