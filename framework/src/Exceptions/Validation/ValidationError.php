<?php

namespace DJWeb\Framework\Exceptions\Validation;

use DJWeb\Framework\Exceptions\BaseRuntimeError;

class ValidationError extends BaseRuntimeError
{
    public function __construct(
        public protected(set) array $validationErrors,
        string $message = "Validation Error"
    ){
        parent::__construct($message);
    }
}