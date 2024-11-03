<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\UploadedFile;

use DJWeb\Framework\Http\UploadedFile\Handlers\FileHandler;
use DJWeb\Framework\Http\UploadedFile\Handlers\HandlerContract;
use DJWeb\Framework\Http\UploadedFile\Handlers\StreamHandler;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class BaseUploadedFile implements UploadedFileInterface
{
    private HandlerContract $handler;

    public function __construct(
        StreamInterface|string $streamOrFile,
        protected int $size,
        protected int $error,
        protected ?string $clientFilename = null,
        protected ?string $clientMediaType = null
    ) {
        $this->handler = is_string($streamOrFile)
            ? new FileHandler($streamOrFile)
            : new StreamHandler($streamOrFile);
    }

    public function moveTo(string $targetPath): void
    {
        $this->handler->moveTo($targetPath);
    }

    public function getStream(): StreamInterface
    {
        return $this->handler->getStream();
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
}
