<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\UploadedFile\Handlers;

use Psr\Http\Message\StreamInterface;

interface HandlerContract
{
    public function moveTo(string $targetPath): void;
    public function getStream(): StreamInterface;
}
