<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console;

use DJWeb\Framework\Console\Resolvers\CommandResolver;
use DJWeb\Framework\Console\Utils\CommandNamespace;
use DJWeb\Framework\ServiceProviders\ConsoleServiceProvider;

class Application extends \DJWeb\Framework\Base\Application
{
    private static ?Application $instance = null;

    private Kernel $kernel;

    protected function __construct()
    {
        parent::__construct();
        $this->registerServiceProvider(new ConsoleServiceProvider());
        $this->kernel = new Kernel($this, $this->get(CommandResolver::class));
        $dir = __DIR__ . DIRECTORY_SEPARATOR . 'Commands';
        $namespace = 'DJWeb\\Framework\\Console\\Commands\\';
        $this->registerCommands(new CommandNamespace($namespace, $dir));
    }

    public static function getInstance(): Application
    {
        if (self::$instance === null) {
            self::$instance = new Application();
        }
        return self::$instance;
    }

    public function handle(): int
    {
        $args = $_SERVER['argv'];
        return $this->kernel->handle($args);
    }

    public function registerCommands(CommandNamespace $namespace): void
    {
        (new CommandRegistrar($this))->registerCommands(
            $namespace->namespace,
            $namespace->path
        );
    }
}
