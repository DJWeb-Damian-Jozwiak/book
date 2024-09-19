<?php

declare(strict_types=1);

namespace DJWeb\Framework\Base;

use DJWeb\Framework\Config\ConfigBase;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ServiceProviderContract;
use DJWeb\Framework\Exceptions\Container\ContainerError;

class Application extends Container
{
    protected static ?self $instance = null;
    private string $base_path = '';
    private ConfigContract $config;

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

    public static function getInstance(): Application
    {
        if (self::$instance === null) {
            /** @phpstan-ignore-next-line instance */
            self::$instance = new static();
        }
        return self::$instance;
    }

    public static function withInstance(?self $instance): void
    {
        self::$instance = $instance;
    }

    public function getConfig(): ConfigContract
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
