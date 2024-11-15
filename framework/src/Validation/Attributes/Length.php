<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Length extends ValidationAttribute
{
    public function __construct(
        private readonly int $min,
        private readonly int $max,
        ?string $message = null
    ) {
        $this->message = $message ?? "Field must be between {$min} and {$max} characters";
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function validate(mixed $value, array $data = []): bool
    {
        return new MinLength($this->min)->validate($value, $data) &&
            new MaxLength($this->max)->validate($value, $data);
    }
}
