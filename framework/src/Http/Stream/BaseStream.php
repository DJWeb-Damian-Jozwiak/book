<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Stream;

use Psr\Http\Message\StreamInterface;

abstract class BaseStream implements StreamInterface
{
    public protected(set) mixed $stream = null;
    protected ?int $size = 0;
    protected StreamContentManager $contentManager;
    protected StreamMetaData $metaData;
    private bool $closed = false;

    public function __construct(
        string $stream = 'php://temp',
        string $mode = 'r+'
    ) {
        $this->stream = fopen($stream, $mode);
        /** @phpstan-ignore-next-line */
        $data = fstat($this->stream);
        $this->size = is_array($data) ? $data['size'] : 0;
        $this->contentManager = new StreamContentManager($this);
        $this->metaData = StreamMetaData::fromStream($this);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close(): void
    {
        if (! $this->stream || $this->closed) {
            return;
        }
        $this->closed = true;
        fclose($this->stream);
    }

    public function withStream(mixed $stream): void
    {
        $this->stream = $stream;
    }

    public function rewind(): void
    {
        rewind($this->stream);
    }
}
