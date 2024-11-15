<?php

declare(strict_types=1);

namespace DJWeb\Framework\Exceptions\Validation;

use DJWeb\Framework\Exceptions\BaseRuntimeError;

class ValidationError extends BaseRuntimeError
{
    /**
     * @param array<int|string, \DJWeb\Framework\Validation\ValidationError> $validationErrors
     * @param string $message
     */
    public function __construct(
        public protected(set) array $validationErrors,
        string $message = 'Validation Error'
    ) {
        parent::__construct($message);
    }

}
