<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EndsWith extends ValidationAttribute
{
    /**
     * @var list<string>
     */
    private readonly array $suffixes;

    /**
     * @param list<mixed> $suffixes
     * @param string|null $message
     */
    public function __construct(
        array $suffixes,
        ?string $message = null
    ) {
        $this->suffixes = array_filter($suffixes, is_string(...));
        $this->message = $message ?? 'Invalid suffix';
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function validate(mixed $value, array $data = []): bool
    {
       return array_filter($this->suffixes, static fn ($suffix) => str_ends_with($value, $suffix)) !== [];
    }
}
