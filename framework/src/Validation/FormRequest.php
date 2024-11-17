<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation;

use DJWeb\Framework\Exceptions\Validation\ValidationError;
use DJWeb\Framework\Http\Request\Psr7\Request;
use DJWeb\Framework\Validation\Attributes\IsValidated;
use ReflectionProperty;

abstract class FormRequest extends Request
{
    protected ValidationResult $validationResult;
    public function validate(): ValidationResult
    {
        $validator = new AttributeValidator();
        $this->validationResult = $validator->validate($this);
        if(! $this->validationResult->isValid()) {
            throw new ValidationError($this->validationResult->errors);
        }
        return $this->validationResult;
    }
    public function populateProperties(): self
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $properties = array_filter($properties, $this->hasIsValidatedAttribute(...));
        $properties = array_filter($properties, $this->propertyProvided(...));
        $properties = array_filter($properties, $this->propertyHasType(...));
        $data = $this->baseData();
        foreach ($properties as $property)
        {
            $value = $data[$property->getName()];
            $propertyName = $property->getName();

            $this->{$propertyName} = $value;
        }
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    private function baseData(): array
    {
        return [
            ...$this->getQueryParams(),
            ...$this->getParsedBody(),
        ];
    }

    private function propertyHasType(ReflectionProperty $property): bool
    {
        $type = $property->getType();
        return $type instanceof \ReflectionNamedType;
    }

    private function hasIsValidatedAttribute(\ReflectionProperty $property): bool
    {
        $attributes = $property->getAttributes(IsValidated::class);
        return (bool) ($attributes);
    }

    private function propertyProvided(\ReflectionProperty $property): bool
    {
        $property_name = $property->getName();
        return isset($this->baseData()[$property_name]);
    }
}
