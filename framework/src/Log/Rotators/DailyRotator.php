<?php

declare(strict_types=1);

namespace DJWeb\Framework\Log\Rotators;

use Carbon\Carbon;
use DirectoryIterator;
use DJWeb\Framework\Exceptions\Log\LoggerError;
use DJWeb\Framework\Log\Contracts\RotatorContract;

readonly class DailyRotator implements RotatorContract
{
    public function __construct(
        private int $maxDays = 7,
        private readonly ?string $pattern = null
    ) {
    }

    public function shouldRotate(string $logPath): bool
    {
        if (! file_exists($logPath)) {
            return false;
        }
        $now = Carbon::now();
        $fromFile = Carbon::now()->setTimestamp((int) filemtime($logPath));

        return $now->toDateString() !== $fromFile->toDateString();
    }

    public function rotate(string $logPath): string
    {
        $info = pathinfo($logPath);
        return sprintf(
            '%s/%s-%s.%s',
            $info['dirname'] ?? '',
            $info['filename'],
            date('Y-m-d'),
            $info['extension'] ?? ''
        );
    }

    public function cleanup(string $logPath): void
    {
        $pattern = $this->pattern ?? '/^\w+\-\d{4}\-\d{2}\-\d{2}\.\w+$/';
        $threshold = strtotime("-{$this->maxDays} days");

        $files = new DirectoryIterator(dirname($logPath));
        $files = iterator_to_array($files);
        $files = array_filter($files, static fn ($file) => (bool) preg_match($pattern, $file->getFilename()));
        $files = array_filter(
            $files,
            static fn ($file) => ! in_array($file->getFilename(), ['.', '..']) && $file->getMTime() < $threshold
        );
        array_walk($files, static fn ($file) => unlink($file->getPathname()));
    }

}
