<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Resolvers;

use DJWeb\Framework\Console\Command;
use ReflectionClass;

class CommandPropertyResolver
{
    /**
     * @return array<string, object>
     */
    public function findPropertiesWithAttribute(Command $command, string $attributeName): array
    {
        $reflection = new ReflectionClass($command);
        $properties = $reflection->getProperties();
        $result = [];
        foreach ($properties as $property) {
            $attributes = $property->getAttributes($attributeName);
            foreach ($attributes as $attribute) {
                $result[$property->getName()] = $attribute->newInstance();
            }
        }
        return $result;
    }
}
