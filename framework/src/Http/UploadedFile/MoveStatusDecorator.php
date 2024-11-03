<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\UploadedFile;

use RuntimeException;

class MoveStatusDecorator extends UploadedFileDecorator
{
    private bool $moved = false;

    public function moveTo(string $targetPath): void
    {
        if ($this->moved) {
            throw new RuntimeException('Cannot move file; already moved!');
        }

        parent::moveTo($targetPath);
        $this->moved = true;
    }
}
