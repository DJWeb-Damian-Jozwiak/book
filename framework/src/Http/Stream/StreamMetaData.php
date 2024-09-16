<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Stream;

readonly class StreamMetaData
{
    /**
     * @param array<int, string> $wrapper_data
     */
    private function __construct(
        public bool $timed_out = false,
        public bool $blocked = false,
        public bool $eof = false,
        public array $wrapper_data = [],
        public string $wrapper_type = '',
        public string $stream_type = '',
        public string $uri = '',
        private string $mode = 'r',
        private bool $seekable = false,
        public int $unread_bytes = 0,
    ) {
    }

    public static function fromStream(BaseStream $stream): StreamMetaData
    {
        $meta = stream_get_meta_data($stream->getStream());
        return new StreamMetaData(...$meta);
    }

    public function isReadable(): bool
    {
        return str_contains($this->mode, 'r') || $this->isWritable();
    }

    public function isWritable(): bool
    {
        return str_contains($this->mode, 'w');
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }
}