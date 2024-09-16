<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use ArrayObject;

/**
 * @extends ArrayObject<string, array<int, string>>
 */
class HeaderArray extends \ArrayObject
{
    /**
     * @param array<string, string|array<int, string>> $headers
     */
    public function __construct(array $headers = [])
    {
        parent::__construct([], ArrayObject::ARRAY_AS_PROPS);
        foreach ($headers as $name => $values) {
            $this->set($name, $values);
        }
    }

    /**
     * @param string|array<int, string> $values
     */
    public function set(string $name, string|array $values): void
    {
        $name = $this->normalizeHeaderName($name);
        $values = $this->normalizeHeaderValues($values);
        $this[$name] = $values;
    }

    /**
     * @return array<int, string>
     */
    public function get(string $name): array
    {
        $name = $this->normalizeHeaderName($name);
        return $this[$name] ?? [];
    }

    public function has(string $name): bool
    {
        $name = $this->normalizeHeaderName($name);
        return isset($this[$name]);
    }

    public function remove(string $name): void
    {
        $name = $this->normalizeHeaderName($name);
        unset($this[$name]);
    }

    /**
     * @param string|array<int, string> $values
     */
    public function add(string $name, string|array $values): void
    {
        $name = $this->normalizeHeaderName($name);
        $values = $this->normalizeHeaderValues($values);
        if (! isset($this[$name])) {
            $this[$name] = [];
        }
        /** @phpstan-ignore-next-line */
        $this[$name] = array_merge($this[$name], $values);
    }

    public function getLine(string $name): string
    {
        return implode(', ', $this->get($name));
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function all(): array
    {
        return $this->getArrayCopy();
    }

    private function normalizeHeaderName(string $name): string
    {
        return $name;
    }

    /**
     * @param string|array<int, string> $values
     *
     * @return array<int, string>
     */
    private function normalizeHeaderValues(string|array $values): array
    {
        if (is_string($values)) {
            return [$values];
        }
        return array_values($values);
    }
}
