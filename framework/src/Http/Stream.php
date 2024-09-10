<?php

namespace DJWeb\Framework\Http;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    private mixed $stream;
    private ?int $size;
    public function __construct(mixed $stream = null)
    {
        $this->stream = $stream ?? fopen('php://temp', 'r+');
        $data = fstat($this->stream);
        $this->size = is_array($data) ? $data['size'] : 0;
    }

    public function __destruct()
    {
       $this->close();
    }
    public function __toString(): string
    {
        return $this->getContents();
    }

    public function getContents(): string
    {
        $content = stream_get_contents($this->stream, -1, 0);
        if (!$content) {
            return '';
        }
        return $content;
    }
    public function setContent(string $content): StreamInterface
    {
        $stream = $this->openStream('w+b');
        if (!$stream) {
            throw new \RuntimeException('Unable to open temp file');
        }
        fwrite($stream, $content);
        $this->stream = $stream;
        $this->rewind();
        return $this;
    }

    protected function openStream(string $mode): mixed
    {
        return fopen('php://temp', $mode);
    }
    public function close(): void
    {
        if(!$this->stream) {
            return;
        }
        fclose($this->stream);
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
        $meta = stream_get_meta_data($this->stream);
        return $meta['seekable'];
    }
    public function seek($offset, $whence = SEEK_SET): void
    {
        fseek($this->stream, $offset, $whence);
    }
    public function rewind(): void
    {
        rewind($this->stream);
    }
    public function isWritable(): bool
    {
        $meta = stream_get_meta_data($this->stream);
        return str_contains($meta['mode'], 'w');
    }
    public function write($string): int
    {
        $size = fwrite($this->stream, $string);
        return $size ? $size : 0;
    }
    public function isReadable(): bool
    {
        $meta = stream_get_meta_data($this->stream);
        return str_contains($meta['mode'], 'r') || $this->isWritable();
    }
    public function read($length): string
    {
        /** @phpstan-ignore argument.type */
        $data = fread($this->stream, $length);
        return $data ? $data : '';
    }

    public function getMetadata($key = null)
    {
        $meta = stream_get_meta_data($this->stream);
        return $key ? ($meta[$key] ?? null) : $meta;
    }
}