<?php

declare(strict_types=1);

namespace DJWeb\Framework\Cache;

use Carbon\Carbon;
use DateInterval;
use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{

    private mixed $value = null;
    private bool $isHit = false;
    private ?Carbon $expiry = null;

    public function __construct(
        private string $key,
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }


    public function get(): mixed
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->isHit;
    }

    public function set(mixed $value): static
    {
        return (clone $this)->with(value: $value);
    }

    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        return (clone $this)->with(
            expiry: $expiration ? Carbon::instance($expiration) : null
        );
    }

    public function expiresAfter(\DateInterval|int|null $time): static
    {
        $expiry = match(true) {
            $time === null => null,
            $time instanceof DateInterval => Carbon::now()->add($time),
            default => Carbon::now()->addSeconds($time),
        };

        return (clone $this)->with(expiry: $expiry);
    }

    public function withIsHit(bool $hit): static
    {
        return (clone $this)->with(isHit: $hit);
    }

    public function getExpiry(): ?Carbon
    {
        return $this->expiry;
    }

    private function with(
        mixed $value = null,
        ?Carbon $expiry = null,
        ?bool $isHit = null,
    ): static {
        $clone = clone $this;
        $clone->value = $value ?? $this->value;
        $clone->expiry = $expiry ?? $this->expiry;
        $clone->isHit = $isHit ?? $this->isHit;
        return $clone;
    }

}