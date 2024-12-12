<?php

declare(strict_types=1);

namespace DJWeb\Framework\Cache;

use Carbon\Carbon;
use DJWeb\Framework\Cache\Contracts\StorageContract;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheItemPool implements CacheItemPoolInterface
{
    private array $deferred = [];

    public function __construct(
        private readonly StorageContract $storage
    ) {
    }

    public function getItem(string $key): CacheItemInterface
    {
        if (isset($this->deferred[$key])) {
            return $this->deferred[$key];
        }

        $item = new CacheItem($key);

        if ($data = $this->storage->get($key)) {
            if ($data['expiry'] === null || $data['expiry'] > Carbon::now()->getTimestamp()) {
                return $item
                    ->set($data['value'])
                    ->withIsHit(true)
                    ->expiresAt(
                        $data['expiry'] ? Carbon::createFromTimestamp($data['expiry']) : null
                    );
            }
            $this->storage->delete($key);
        }

        return $item;
    }

    public function getItems(array $keys = []): iterable
    {
        return array_combine(
            $keys,
            array_map(fn (string $key) => $this->getItem($key), $keys)
        );
    }

    public function hasItem(string $key): bool
    {
        return $this->getItem($key)->isHit();
    }

    public function clear(): bool
    {
        $this->deferred = [];
        return $this->storage->clear();
    }

    public function deleteItem(string $key): bool
    {
        unset($this->deferred[$key]);
        return $this->storage->delete($key);
    }

    public function deleteItems(array $keys): bool
    {
        return ! in_array(
            false,
            array_map(fn ($key) => $this->deleteItem($key), $keys),
            true
        );
    }

    public function save(CacheItemInterface $item): bool
    {
        return $this->storage->set($item->getKey(), [
            'value' => $item->get(),
            'expiry' => $item->getExpiry()?->timestamp,
        ]);
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[$item->getKey()] = $item;
        return true;
    }

    public function commit(): bool
    {
        if (array_any($this->deferred, fn ($item) => ! $this->save($item))) {
            return false;
        }
        $this->deferred = [];
        return true;
    }
}
