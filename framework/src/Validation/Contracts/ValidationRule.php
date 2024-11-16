<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Contracts;

interface ValidationRule
{
    public function validate(mixed $value): bool;

}
