<?php

namespace DJWeb\Framework\Config;

use DJWeb\Framework\Application;

class Config
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return Application::getInstance()->getConfig()->get($key, $default);
    }

    public static function set(string $key, mixed $value): void
    {
        Application::getInstance()->getConfig()->set($key, $value);
    }
}