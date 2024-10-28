<?php

declare(strict_types=1);

namespace DJWeb\Framework\Base;

use DJWeb\Framework\Config\ConfigBase;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\Contracts\ServiceProviderContract;
use DJWeb\Framework\Exceptions\Container\ContainerError;

class Application extends Container
{
    public string $base_path{
        get => $this->getBinding('base_path') ?? '';
    }

    public ?ContainerContract $config{
        get {
            $this->config ??= $this->get(ContainerContract::class);
            $this->config->loadConfig();
            return $this->config;
        }
    }
    protected static ?self $instance = null;

    protected function __construct()
    {
        parent::__construct();
        $this->set(Container::class, $this);
        $this->set(ContainerContract::class, new ConfigBase($this));
    }

    public function __clone()
    {
        throw new ContainerError('Cannot clone Application');
    }

    public function __serialize(): array
    {
        return [];
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

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            /** @phpstan-ignore-next-line instance */
            self::$instance = new static();
        }
        /** @phpstan-ignore-next-line instance */
        return self::$instance;
    }

    public static function withInstance(?self $instance): void
    {
        self::$instance = $instance;
    }

    public function getConfig(): ?ConfigContract
    {
        return $this->config;
    }

    protected function registerServiceProvider(
        ServiceProviderContract $provider
    ): void {
        $provider->register($this);
    }
}
