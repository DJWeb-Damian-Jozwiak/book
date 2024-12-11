<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling;

class CodeSnippet
{
    /**
     * @var array<int, string>
     */
    public private(set) array $lines = [];
    public private(set) int $startLine;
    public private(set) int $endLine;

    public function __construct(
        public readonly string $file,
        public readonly int $errorLine,
        private int $contextLines = 10,
    ) {
        if ($file && file_exists($file)) {
            $this->processFile();
        }
    }

    private function processFile(): void
    {
        $allLines = file($this->file, FILE_IGNORE_NEW_LINES);
        $this->startLine = max(1, $this->errorLine - $this->contextLines);
        $this->endLine = min(
            count($allLines),
            $this->errorLine + $this->contextLines,
        );
        $this->lines = array_combine(
            range($this->startLine, $this->endLine),
            array_slice(
                $allLines,
                $this->startLine - 1,
                $this->endLine - $this->startLine + 1
            )
        );
    }
}
