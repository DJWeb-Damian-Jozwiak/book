<?php

declare(strict_types=1);

namespace DJWeb\Framework\Storage;

use Carbon\Carbon;

final readonly class CookieOptions
{
    public int $expires;
    public bool $secure;
    public function __construct(
        $expires = 0,
        public string $path = '/',
        public string $domain = '',
        ?bool $secure = null,
        public bool $httponly = true,
        public string $samesite = 'Lax',
    ) {
        $secure ??= isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $this->secure = $secure;
        $this->expires = $expires ? $expires : Carbon::now()->addHour()->getTimestamp();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'expires' => $this->expires,
            'path' => $this->path,
            'domain' => $this->domain,
            'secure' => $this->secure,
            'httponly' => $this->httponly,
            'samesite' => $this->samesite,
        ];
    }
}
