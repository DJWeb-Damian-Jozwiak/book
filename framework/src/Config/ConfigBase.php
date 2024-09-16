<?php

declare(strict_types=1);

namespace DJWeb\Framework\Config;

use DJWeb\Framework\Application;
use Dotenv\Dotenv;

class ConfigBase
{
    /**
     * @var array<string, string|float|int|null>
     */
    private array $config = [];

    public function __construct(private readonly Application $app)
    {
        $this->loadEnvironmentVariables();
        $this->loadConfigFiles();
    }

    public function get(
        string $key,
        string|float|int|null $default = null
    ): string|float|int|null {
        $keys = explode('.', $key);
        $config = $this->config;

        foreach ($keys as $segment) {
            if (isset($config[$segment])) {
                $config = $config[$segment];
            } else {
                return $default;
            }
        }

        return $config;
    }

    public function set(string $key, string|float|int|null $value): void
    {
        $keys = explode('.', $key);
        $this->setRecursive($this->config, $keys, $value);
    }

    private function loadEnvironmentVariables(): void
    {
        $dotenv = Dotenv::createImmutable($this->app->base_path);
        $dotenv->load();
    }

    private function loadConfigFiles(): void
    {
        $configPath = $this->app->base_path . DIRECTORY_SEPARATOR . 'config';
        $files = scandir($configPath) ?: [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $key = pathinfo($file, PATHINFO_FILENAME);
                $this->config[$key] = require $configPath . DIRECTORY_SEPARATOR . $file;
            }
        }
    }

    /**
     * @param array<string, string|float|int|null> $array
     * @param array<int, string> $keys
     * @param string|float|int|null $value
     *
     * @return void
     */
    private function setRecursive(
        array &$array,
        array $keys,
        string|float|int|null $value
    ): void {
        $key = array_shift($keys);

        if (empty($keys)) {
            $array[$key] = $value;
        } else {
            if (! isset($array[$key])) {
                $array[$key] = [];
            }
            $this->setRecursive($array[$key], $keys, $value);
        }
    }
}
