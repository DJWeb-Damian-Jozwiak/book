<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\UploadedFile\Handlers;

use DJWeb\Framework\Http\Stream;
use Psr\Http\Message\StreamInterface;

class StreamHandler implements HandlerContract
{
    public function __construct(private StreamInterface $stream)
    {
    }

    public function moveTo(string $targetPath): void
    {
        $destination = new Stream($targetPath, 'w');
        while (! $this->stream->eof()) {
            $destination->write($this->stream->read(4096));
        }
    }

    public function getStream(): StreamInterface
    {
        return $this->stream;
    }
}
