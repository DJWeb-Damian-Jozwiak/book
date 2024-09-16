<?php

declare(strict_types=1);

namespace DJWeb\Framework\Base;

use DJWeb\Framework\Config\ConfigBase;
use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ServiceProviderContract;
use DJWeb\Framework\Exceptions\Container\ContainerError;

class Application extends Container
{
    private string $base_path = '';
    private ConfigBase $config;

    public function __clone()
    {
        throw new ContainerError('Cannot clone Application');
    }

    public function __serialize(): array
    {
        return [
            'base_path' => $this->base_path,
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        json_encode($data, JSON_THROW_ON_ERROR);
        throw new ContainerError('Cannot unserialize Application');
    }

    public function getConfig(): ConfigBase
    {
        return $this->config;
    }

    public function addBasePath(string $base_path): void
    {
        $this->base_path = $base_path;
    }

    public function getBasePath(): string
    {
        return $this->base_path;
    }

    public function loadConfig(): void
    {
        $this->config = new ConfigBase($this);
    }

    protected function registerServiceProvider(
        ServiceProviderContract $provider
    ): void {
        $provider->register($this);
    }
}
