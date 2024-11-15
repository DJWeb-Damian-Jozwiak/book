<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Contracts;

use DJWeb\Framework\Validation\FormRequest;
use DJWeb\Framework\Validation\ValidationResult;

interface Validator
{
    public function validate(FormRequest $request): ValidationResult;
}
