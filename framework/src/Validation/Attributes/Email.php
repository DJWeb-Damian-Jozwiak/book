<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Email extends ValidationAttribute
{
    public function __construct(?string $message = null)
    {
        $this->message = $message ?? 'Invalid email format';
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $data
     * @return bool
     */
    public function validate(mixed $value, array $data = []): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}