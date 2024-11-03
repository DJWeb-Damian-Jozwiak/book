<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\UploadedFile;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFileFactory
{
    public static function createUploadedFile(
        StreamInterface|string $streamOrFile,
        int $size,
        int $error,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ): UploadedFileInterface {
        $baseFile = new BaseUploadedFile($streamOrFile, $size, $error, $clientFilename, $clientMediaType);

        $file = new ErrorValidatorDecorator($baseFile);
        $file = new MoveStatusDecorator($file);
        return new PathValidatorDecorator($file);
    }
}
