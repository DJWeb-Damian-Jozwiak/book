<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Request;

use Psr\Http\Message\UploadedFileInterface;

final class UploadedFilesManager
{
    /**
     * @param array<string, mixed> $uploadedFiles
     */
    public function __construct(public private(set) array $uploadedFiles = [])
    {
        $this->validateUploadedFiles();
    }

    /**
     * @param array<string, mixed> $uploadedFiles
     */
    public function withUploadedFiles(array $uploadedFiles): self
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        $new->validateUploadedFiles();
        return $new;
    }

    private function validateUploadedFiles(): void
    {
        $this->uploadedFiles = array_filter(
            $this->uploadedFiles,
            static fn (mixed $file) => $file instanceof UploadedFileInterface
        );
    }
}
