<?php

declare(strict_types=1);

namespace App\FormValidators;

use DJWeb\Framework\Validation\Attributes\Email;
use DJWeb\Framework\Validation\Attributes\IsValidated;
use DJWeb\Framework\Validation\Attributes\MaxLength;
use DJWeb\Framework\Validation\Attributes\Required;
use DJWeb\Framework\Validation\FormRequest;

class ForgotPasswordDTO extends FormRequest
{
    #[Email]
    #[Required]
    #[IsValidated]
    #[MaxLength(255)]
    public protected(set) string $email;

    public function toArray(): array
    {
        return [
            'email' => $this->email,
        ];
    }
}