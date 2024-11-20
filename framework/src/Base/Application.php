<?php

declare(strict_types=1);

namespace DJWeb\Framework\Base;

use DJWeb\Framework\Config\ConfigBase;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\Contracts\ServiceProviderContract;
use DJWeb\Framework\DBAL\Contracts\Schema\SchemaContract;
use DJWeb\Framework\Exceptions\Container\ContainerError;
use DJWeb\Framework\Log\LoggerFactory;
use DJWeb\Framework\ServiceProviders\SchemaServiceProvider;
use Psr\Log\LoggerInterface;

class Application extends Container
{
    public string $base_path{
        get => $this->getBinding('base_path') ?? '';
    }

    public SchemaContract $schema{
        get => $this->get(SchemaContract::class);
    }

    public ?ConfigContract $config{
        get {
            $this->config ??= $this->get(ConfigContract::class);
            $this->config->loadConfig();
            return $this->config;
        }
    }

    public LoggerInterface $logger{
        get {
            if($this->has(LoggerInterface::class)) {
                $logger = $this->get(LoggerInterface::class);
            } else {
                $logger = LoggerFactory::create();
            }
            $this->_logger ??= $logger;
            $this->set(LoggerInterface::class, $this->_logger);
            return $this->_logger;
        }
    }
    protected static ?self $instance = null;
    private ?LoggerInterface $_logger;
    protected function __construct()
    {
        parent::__construct();
        $this->set(Container::class, $this);
        $this->set(ContainerContract::class, $this);
        $this->set(ConfigContract::class, new ConfigBase($this));
        $this->registerServiceProvider(new SchemaServiceProvider());
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
