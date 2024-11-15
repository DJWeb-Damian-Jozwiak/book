<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation;

use Closure;

class ValueCaster
{
    final public function cast(string $type, mixed $value): mixed
    {
        return ($this->maps()[$type])($value) ?? $value;
    }

    /**
     * @return array<string, Closure>
     */
    protected function maps(): array
    {
        return [
            'int' => intval(...),
            'float' => floatval(...),
            'bool' => $this->toBool(...),
            'string' => strval(...),
            'array' => $this->toArray(...),
        ];
    }

    private function toBool(mixed $value): bool
    {
        $bools = ['true', true, 1, '1', 'on', 'yes', 'y'];
return in_array($value, $bools, true);
    }

    /**
     * @param mixed $value
     * @return array<int|string, mixed>
     */
    private function toArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;

        }
        if (is_string($value)) {
            return json_decode($value, true);

        }
        return (array) $value;
    }

}
