<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use DJWeb\Framework\Http\Stream\BaseStream;
use DJWeb\Framework\Http\Stream\StreamMetaData;
use Psr\Http\Message\StreamInterface;

class Stream extends BaseStream
{
    public function __toString(): string
    {
        return $this->getContents();
    }

    public function getContents(): string
    {
        return $this->contentManager->getContents();
    }

    public function withContent(string $content): StreamInterface
    {
        return $this->contentManager->withContent($content);
    }

    public function detach()
    {
        $stream = $this->stream;
        $this->stream = null;
        $this->size = null;
        return $stream;
    }

    public function getSize(): ?int
    {
        $data = fstat($this->stream);
        $this->size = is_array($data) ? $data['size'] : 0;
        return $this->size;
    }

    public function tell(): int
    {
        $size = ftell($this->stream);
        return $size ? $size : 0;
    }

    public function eof(): bool
    {
        return feof($this->stream);
    }

    public function isSeekable(): bool
    {
        return $this->metaData->isSeekable();
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        fseek($this->stream, $offset, $whence);
    }

    public function isWritable(): bool
    {
        return $this->metaData->isWritable();
    }

    public function write($string): int
    {
        $size = fwrite($this->stream, $string);
        return $size ? $size : 0;
    }

    public function isReadable(): bool
    {
        return $this->metaData->isReadable();
    }

    public function read($length): string
    {
        /** @phpstan-ignore argument.type */
        $data = fread($this->stream, $length);
        return $data ? $data : '';
    }

    /**
     * @param $key
     * @return array<int, string>|bool|StreamMetaData|int|string
     */
    public function getMetadata($key = null): array|bool|StreamMetaData|int|string
    {
        $metadata = (array)$this->metaData;
        return $metadata[$key] ?? $this->metaData;
    }
}
