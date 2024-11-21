<?php

declare(strict_types=1);

namespace DJWeb\Framework\Storage;

use DJWeb\Framework\Base\Application;

final class EnvFileHandler
{
    private const ENV_FILE = '.env';

    public function update(string $key, string $value): void
    {
        $path = $this->getEnvPath();
        File::ensureFileExists($path);
        $content = file_get_contents($path);
        $content = preg_replace('/^' . $key . '=.*$/m', '', $content);
        $content .= "\n" . $key . '=' . $value;
        file_put_contents($path, $content);
    }

    private function getEnvPath(): string
    {
        $app = Application::getInstance();
        return $app->base_path . DIRECTORY_SEPARATOR . self::ENV_FILE;
    }

}
