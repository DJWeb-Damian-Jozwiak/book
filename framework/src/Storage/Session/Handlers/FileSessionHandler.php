<?php

namespace DJWeb\Framework\Storage\Session\Handlers;

use DJWeb\Framework\Storage\Directory;
use DJWeb\Framework\Storage\File;
use DJWeb\Framework\Storage\Session\Contracts\SessionStorageContract;
use DJWeb\Framework\Storage\Session\SessionSecurity;

final readonly class FileSessionHandler implements \SessionHandlerInterface
{
    public function __construct(private string $savePath, private SessionSecurity $security)
    {
        Directory::ensureDirectoryExists($this->savePath);
        Directory::ensureDirectoryIsWritable($this->savePath);
        //dump($this->security);
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string
    {
        $file = $this->getFilePath($id);
        if (!file_exists($file)) {
            return '';
        }
        File::ensureFileIsReadable($file);
        $encrypted = file_get_contents($file);
        /** @var string $decrypted */
        $decrypted = $this->security->decrypt($encrypted);
        return $decrypted;
    }

    public function write(string $id, string $data): bool
    {
        $file = $this->getFilePath($id);
        $encrypted = $this->security->encrypt($data);

        return file_put_contents($file, $encrypted) !== false;
    }

    public function destroy(string $id): bool
    {
        $file = $this->getFilePath($id);
        File::unlink($file);

        return true;
    }

    public function gc(int $max_lifetime): int
    {
        $files = glob($this->savePath . '/sess_*');
        $files = array_filter($files, fn(string $file) => filemtime($file) + $max_lifetime < time() && unlink($file));
        array_walk($files, fn(string $file) => unlink($file));
        return count($files);
    }

    private function getFilePath(string $id): string
    {
        return $this->savePath . '/sess_' . $id;
    }
}