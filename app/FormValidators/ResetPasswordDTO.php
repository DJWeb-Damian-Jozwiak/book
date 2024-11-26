<?php

namespace App\FormValidators;

use DJWeb\Framework\Validation\Attributes\IsValidated;
use DJWeb\Framework\Validation\Attributes\Length;
use DJWeb\Framework\Validation\Attributes\Required;
use DJWeb\Framework\Validation\FormRequest;

class ResetPasswordDTO extends FormRequest
{
    #[Required]
    #[IsValidated]
    public protected(set) string $token;

    #[Required]
    #[IsValidated]
    #[Length(min: 8, max: 255)]
    public protected(set) string $password;

    #[Required]
    #[IsValidated]
    #[Same('password')]
    public protected(set) string $password_confirmation;

    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'password' => $this->password,
        ];
    }
}