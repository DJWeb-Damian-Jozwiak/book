<?php

namespace App\Database\FormRequests;

use DJWeb\Framework\Validation\Attributes\IsValidated;
use DJWeb\Framework\Validation\Attributes\MaxLength;
use DJWeb\Framework\Validation\Attributes\MinLength;
use DJWeb\Framework\Validation\Attributes\Required;
use DJWeb\Framework\Validation\FormRequest;

class CategoryRequest extends FormRequest
{
    #[MinLength(3)]
    #[MaxLength(20)]
    #[Required]
    public protected(set) string $name = '';

    #[MinLength(50)]
    public protected(set) string $description = '';

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}