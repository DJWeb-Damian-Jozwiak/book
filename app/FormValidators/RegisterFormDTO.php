<?php

declare(strict_types=1);

namespace App\FormValidators;

use DJWeb\Framework\Validation\Attributes\Email;
use DJWeb\Framework\Validation\Attributes\IsValidated;
use DJWeb\Framework\Validation\Attributes\Length;
use DJWeb\Framework\Validation\Attributes\MaxLength;
use DJWeb\Framework\Validation\Attributes\Required;
use DJWeb\Framework\Validation\FormRequest;

class RegisterFormDTO extends FormRequest
{
    #[Email]
    #[Required]
    #[IsValidated]
    #[MaxLength(255)]
    public protected(set) string $email;

    #[Required]
    #[IsValidated]
    #[Length(min: 3, max: 100)]
    public protected(set) string $username;

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
            'email' => $this->email,
            'username' => $this->username,
            'password' => password_hash($this->password, PASSWORD_ARGON2ID),
        ];
    }
}