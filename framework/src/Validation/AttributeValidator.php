<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation;

use DJWeb\Framework\Validation\Contracts\ValidationRule;
use DJWeb\Framework\Validation\Contracts\Validator;

class AttributeValidator implements Validator
{
    public function validate(object $request): ValidationResult
    {
        $result = new ValidationResult();
        $properties = $this->getValidatableProperties($request);
        $data = $this->extractData($request);
        foreach ($properties as $property) {
            $this->validateProperty($property, $request, $data, $result);
        }
        return $result;
    }

    /**
     * @return array<int, \ReflectionProperty>
     */
    private function getValidatableProperties(object $request): array
    {
        $reflection = new \ReflectionClass($request);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        return array_filter($properties, $this->hasValidationRules(...));
    }

    private function hasValidationRules(\ReflectionProperty $property): bool
    {
        $attributes = $property->getAttributes();
        return array_any($attributes, static function (\ReflectionAttribute $attribute) {
            $rule = $attribute->newInstance();
            return $rule instanceof ValidationRule;
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    private function validateProperty(
        \ReflectionProperty $property,
        object              $request,
        array               $data,
        ValidationResult    $result
    ): void
    {
        $attributes = $property->getAttributes();
        $value = $data[$property->getName()] ?? null;
        foreach ($attributes as $attribute) {
            /** @var ValidationRule $rule */
            $rule = $attribute->newInstance();
            if (!$rule->validate($value, $data)) {
                $result->addError(
                    $property->getName(),
                    $rule->message
                );

            }

        }
    }

    private function extractData(object $request): array
    {
        return get_object_vars($request);
    }

}
