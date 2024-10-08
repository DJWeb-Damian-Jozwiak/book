<?php

declare(strict_types=1);

namespace DJWeb\Framework;

use DJWeb\Framework\Config\ConfigBase;
use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ServiceProviderContract;
use DJWeb\Framework\Exceptions\Container\ContainerError;
use DJWeb\Framework\Http\Kernel;
use DJWeb\Framework\ServiceProviders\HttpServiceProvider;
use DJWeb\Framework\ServiceProviders\RouterServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application extends Container
{
    public string $base_path{
        get => $this->getBinding('base_path') ?? '';
    }

    public ?ConfigBase $config{
        get {
            $this->config ??= new ConfigBase($this);
            return $this->config;
        }
    }
    private static ?Application $instance = null;
    private Kernel $kernel;

    private function __construct()
    {
        parent::__construct();
        $this->set(Container::class, $this);
        $this->registerServiceProvider(new HttpServiceProvider());
        $this->registerServiceProvider(new RouterServiceProvider());
        $this->kernel = new Kernel($this);
    }

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
        json_encode($data, flags: JSON_THROW_ON_ERROR);
        throw new ContainerError('Cannot unserialize Application');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
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
        ServiceProviderContract $provider
    ): void {
        $provider->register($this);
    }
}
