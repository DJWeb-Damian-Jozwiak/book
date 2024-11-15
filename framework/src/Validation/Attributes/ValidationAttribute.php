<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Attributes;

use Attribute;
use DJWeb\Framework\Validation\Contracts\ValidationRule;

abstract class ValidationAttribute implements ValidationRule
{
    public string $message {
        get {
            return $this->message;
        }
        set => $this->message = $value;
    }
}