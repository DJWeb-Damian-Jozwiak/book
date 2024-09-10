<?php
declare(strict_types=1);
namespace DJWeb\Framework\Http\Uri;

trait PathTrait
{
    private string $path = '';
    public function getPath(): string
    {
        return $this->path;
    }
    public function withPath(string $path): self
    {
        return $this->clone($this, 'path', $path);
    }
}