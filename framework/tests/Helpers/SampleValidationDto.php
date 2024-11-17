<?php

namespace Tests\Helpers;

use DJWeb\Framework\Validation\Attributes\Email;
use DJWeb\Framework\Validation\Attributes\EndsWith;
use DJWeb\Framework\Validation\Attributes\IsValidated;
use DJWeb\Framework\Validation\Attributes\Length;
use DJWeb\Framework\Validation\Attributes\Max;
use DJWeb\Framework\Validation\Attributes\Min;
use DJWeb\Framework\Validation\Attributes\Required;
use DJWeb\Framework\Validation\Attributes\StartsWith;
use DJWeb\Framework\Validation\FormRequest;

class SampleValidationDto extends FormRequest
{
    #[Email]
    #[Required]
    #[IsValidated]
    #[EndsWith(['.com'])]
    #[StartsWith(['test'])]
    public protected(set) string $email;

    #[IsValidated]
    #[Length(min: 3, max: 100)]
    public protected(set) string $name;

    #[IsValidated]
    #[Min(18)]
    #[Max(100)]
    public protected(set) int $age;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'age' => $this->age,
        ];
    }
}