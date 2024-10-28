<?php

namespace DJWeb\Framework\Config\Contracts;

interface ConfigContract
{
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
    public function loadConfig(): void;
}