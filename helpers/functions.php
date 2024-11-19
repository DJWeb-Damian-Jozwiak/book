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

if (!function_exists('vite')) {
    function vite($assets): string
    {
        static $manifest = null;

        // Lazy load manifest
        if ($manifest === null) {
            $manifestPath = Application::getInstance()->base_path . '/public/build/manifest.json';
            $manifest = file_exists($manifestPath)
                ? json_decode(file_get_contents($manifestPath), true)
                : [];
        }

        $tags = [];
        $assets = is_array($assets) ? $assets : [$assets];

        // Development
        if (Config::get('app.env') === 'local') {
            $tags[] = '<script type="module" src="http://localhost:5173/@vite/client"></script>';

            foreach ($assets as $asset) {
                $tags[] = '<script type="module" src="http://localhost:5173/' . $asset . '"></script>';
            }

            return implode("\n", $tags);
        }

        // Production
        foreach ($assets as $asset) {
            if (!isset($manifest[$asset])) {
                continue;
            }

            $file = $manifest[$asset]['file'];
            $css = $manifest[$asset]['css'] ?? [];

            // Add CSS files
            foreach ($css as $cssFile) {
                $tags[] = '<link rel="stylesheet" href="/build/' . $cssFile . '">';
            }

            // Add JS file
            $tags[] = '<script type="module" src="/build/' . $file . '"></script>';
        }

        return implode("\n", $tags);
    }
}