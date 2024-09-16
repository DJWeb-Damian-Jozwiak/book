<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Stream;

use Psr\Http\Message\StreamInterface;

readonly class StreamContentManager
{
    public function __construct(private BaseStream $stream)
    {
    }

    public function getContents(): string
    {
        $content = stream_get_contents($this->stream->getStream(), -1, 0);
        if (! $content) {
            return '';
        }
        return $content;
    }

    public function withContent(string $content): StreamInterface
    {
        /** @var resource $stream */
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $content);
        $this->stream->withStream($stream);
        $this->stream->rewind();
        return $this->stream;
    }
}
