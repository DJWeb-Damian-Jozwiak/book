<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\UploadedFile\Handlers;

use DJWeb\Framework\Http\Stream;

class FileHandler implements HandlerContract
{
    public function __construct(private string $file)
    {
    }

    public function moveTo(string $targetPath): void
    {
        PHP_SAPI === 'cli'
            ? rename($this->file, $targetPath) : move_uploaded_file($this->file, $targetPath);
    }

    public function getStream(): Stream
    {
        return new Stream($this->file);
    }
}
