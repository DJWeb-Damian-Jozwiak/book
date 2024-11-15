<?php

namespace DJWeb\Framework\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class StartsWith extends ValidationAttribute
{
    /**
     * @var list<string>
     */
    private readonly array $prefixes;

    /**
     * @param list<mixed> $suffixes
     * @param string|null $message
     */
    public function __construct(
        array $suffixes,
        ?string $message = null
    ) {
        $this->prefixes = array_filter($suffixes, is_string(...));
        $this->message = $message ?? 'Invalid suffix';
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $data
     * @return bool
     */
    public function validate(mixed $value, array $data = []): bool
    {
        return array_filter($this->prefixes, fn($suffix) => str_starts_with($value, $suffix)) !== [];
    }
}