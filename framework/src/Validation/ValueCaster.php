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
            'bool' => $this->toBool(...),
        ];
    }

    private function toBool(mixed $value): bool
    {
        $booleans = ['true', true, 1, '1', 'on', 'yes', 'y'];
        return in_array($value, $booleans, true);
    }
}