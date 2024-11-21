<?php

declare(strict_types=1);

namespace DJWeb\Framework\Storage;

use Carbon\Carbon;
use DJWeb\Framework\Encryption\EncryptionService;

final class CookieManager
{
    public function set(string $name, mixed $value, CookieOptions $options = new CookieOptions()): void
    {
        $encrypted = new EncryptionService()->encrypt($value);
        setcookie($name, $encrypted, $options->toArray());
    }

    public function get(string $name, mixed $default = null): mixed
    {
        $value = $_COOKIE[$name] ?? null;

        if ($value === null) {
            return $default;
        }

        return new EncryptionService()->decrypt($value);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return array_map(new EncryptionService()->decrypt(...), $_COOKIE);
    }

    public function remove(string $name): void
    {
        $past = Carbon::now()->subHour()->getTimestamp();
        setcookie($name, '', new CookieOptions(expires: $past)->toArray());
    }
}
