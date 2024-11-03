<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\UploadedFile;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

class ErrorValidatorDecorator extends UploadedFileDecorator
{
    public function validate(): void
    {
        if ($this->getError() !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Cannot operate on file due to upload error');
        }
    }

    public function getStream(): StreamInterface
    {
        $this->validate();
        return parent::getStream();
    }

    public function moveTo(string $targetPath): void
    {
        $this->validate();
        parent::moveTo($targetPath);
    }
}
