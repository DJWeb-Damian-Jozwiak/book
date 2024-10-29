<?php

namespace DJWeb\Framework\DBAL\Models\Casts;

class ArrayCaster
{
    /**
     * @param array<int|string, mixed>|string $value
     * @return array<int|string, mixed>
     * @throws \JsonException
     */
    public static function cast(array|string $value): array
    {
        if (is_string($value)) {
            return json_decode($value, true, flags: JSON_THROW_ON_ERROR);
        }
        return $value;
    }
}