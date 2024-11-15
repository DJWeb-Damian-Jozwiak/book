<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation\Attributes;

use Attribute;
use DJWeb\Framework\Validation\CompareCondition;

#[Attribute(Attribute::TARGET_PROPERTY)]
class RequiredWhen extends ValidationAttribute
{
    /**
     * @param array<string, array{0: string, 1: int|float}> $conditions
     * @param string|null $message
     */
    public function __construct(
        private readonly array $conditions,
        ?string $message = null
    ) {
        $this->message = $message ?? 'This field is required based on other fields';
    }
    /**
     * @param mixed $value
     * @param array<string, mixed> $data
     * @return bool
     */
    public function validate(mixed $value, array $data = []): bool
    {
        $conditions = $this->conditions;
        $conditions = array_intersect_key($conditions, $data);
        foreach ($conditions as $field => $condition) {
            $compareCondition = new CompareCondition($data[$field]);

            if ($compareCondition->compare(...$condition)) {
                return $value !== null && $value !== '';
            }
        }
        return true;
    }
}