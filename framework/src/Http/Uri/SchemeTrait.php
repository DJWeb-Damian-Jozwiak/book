<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Uri;

use InvalidArgumentException;

trait SchemeTrait
{
    private string $scheme = '';
    public function getScheme(): string
    {
        return $this->scheme;
    }
    public function withScheme(string $scheme): static
    {
        $this->validateScheme($scheme);
        return $this->clone($this, 'scheme', $scheme);
    }
    private function validateScheme(string $scheme): void
    {
        if (! in_array($scheme, ['http', 'https'])) {
            throw new InvalidArgumentException("Invalid scheme: {$scheme}");
        }
    }
}
