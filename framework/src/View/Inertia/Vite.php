<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Inertia;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Web\Application;

class Vite
{
    public function render(array $assets): string
    {
        // Development
        if (Config::get('app.env') === 'local') {
            return $this->devConfig($assets);
        }
        $manifestPath = Application::getInstance()->base_path . '/public/build/.vite/manifest.json';
        $manifest = file_exists($manifestPath)
            ? json_decode(file_get_contents($manifestPath), true) : [];

        return $this->prodConfig($manifest, $assets);
    }

    public function devConfig(array $assets): string
    {
        $tags = [];
        $tags[] = '<script type="module" src="http://localhost:5173/@vite/client"></script>';

        foreach ($assets as $asset) {
            $tags[] = '<script type="module" src="http://localhost:5173/' . $asset . '"></script>';
        }

        return implode("\n", $tags);
    }

    /**
     * @param mixed $manifest
     * @param array $assets
     *
     * @return string
     */
    public function prodConfig(array $manifest, array $assets): string
    {
        $tags = [];
        foreach ($assets as $asset) {
            $file = $manifest[$asset]['file'];
            $tags[] = '<script type="module" src="/build/' . $file . '"></script>';
        }

        return implode("\n", $tags);
    }
}
