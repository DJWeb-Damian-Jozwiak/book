<?php

declare(strict_types=1);

namespace DJWeb\Framework;

use DJWeb\Framework\Config\ConfigBase;
use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ServiceProviderContract;
use DJWeb\Framework\Http\Kernel;
use DJWeb\Framework\ServiceProviders\HttpServiceProvider;
use DJWeb\Framework\ServiceProviders\RouterServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application extends Container
{
    public string $base_path = '';
    private static ?Application $instance = null;
    private ConfigBase $config;
    private Kernel $kernel;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConfig(): ConfigBase
    {
        return $this->config;
    }

    public function __clone()
    {
        throw new ContainerException('Cannot clone Application');
    }

    private function __construct()
    {
        parent::__construct();
        $this->registerServiceProvider(new HttpServiceProvider());
        $this->registerServiceProvider(new RouterServiceProvider());
        $this->kernel = new Kernel($this);
    }

    public function loadConfig(): void
    {
        $this->config = new ConfigBase($this);
    }

    public function handle(): ResponseInterface
    {
        $request = $this->get(ServerRequestInterface::class);
        return $this->kernel->handle($request);
    }

    public function withRoutes(callable $callback): void
    {
        $this->kernel->withRoutes($callback);
    }

    protected function registerServiceProvider(
        ServiceProviderInterface $provider
    ): void {
        $provider->register($this);
    }

    public function __serialize(): array
    {
        return [
            'base_path' => $this->base_path
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        throw new ContainerException('Cannot unserialize Application');
    }
}
