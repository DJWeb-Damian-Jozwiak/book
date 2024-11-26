<?php

namespace App\FormValidators;

use DJWeb\Framework\Validation\Attributes\IsValidated;
use DJWeb\Framework\Validation\Attributes\MaxLength;
use DJWeb\Framework\Validation\Attributes\Required;
use DJWeb\Framework\Validation\FormRequest;

class LoginFormDTO extends FormRequest
{
    #[Required]
    #[IsValidated]
    #[MaxLength(255)]
    public protected(set) string $login;

    #[Required]
    #[IsValidated]
    public protected(set) string $password;

    #[IsValidated]
    public protected(set) bool $remember = false;

    public function toArray(): array
    {
        return [
            'login' => $this->login,
            'password' => $this->password,
            'remember' => $this->remember,
        ];
    }
}