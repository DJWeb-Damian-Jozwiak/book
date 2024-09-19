<?php

declare(strict_types=1);

namespace DJWeb\Framework\Config;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\Base\DotContainer;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use Dotenv\Dotenv;

class ConfigBase extends DotContainer implements ConfigContract
{
    public function __construct(private readonly Application $app)
    {
        parent::__construct();
        $this->loadEnvironmentVariables();
        $this->loadConfigFiles();
    }

    private function loadEnvironmentVariables(): void
    {
        $dotenv = Dotenv::createImmutable($this->app->getBasePath());
        $dotenv->load();
    }

    private function loadConfigFiles(): void
    {
        $configPath = $this->app->getBasePath() . DIRECTORY_SEPARATOR . 'config';
        $files = scandir($configPath) ? scandir($configPath) : [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $key = pathinfo($file, PATHINFO_FILENAME);
                $this->set($key, require $configPath . DIRECTORY_SEPARATOR . $file);
            }
        }
    }
}
