<?php

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Web\Application;
use Psr\Http\Message\ServerRequestInterface;

function env(string $key, mixed $value = null): mixed
{
    return $_ENV[$key] ?? $value;
}

function config(string $name, mixed $value = null): mixed
{
    return Config::get($name, $value);
}

function request(): ServerRequestInterface
{
    return Application::getInstance()->get(ServerRequestInterface::class);
}

function url(string $path = ''): string
{
    $scheme = request()->getUri()->getScheme();
    $host = request()->getUri()->getHost();
    return "{$scheme}://{$host}{$path}";
}

