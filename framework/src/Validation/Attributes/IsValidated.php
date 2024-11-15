<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class IsValidated extends ValidationAttribute
{
    public function validate(mixed $value, array $data = []): bool
    {
        return true;
    }
}
