<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

class CommandRegistrar
{
    private string $commandsNamespace;
    private string $commandsDirectory;

    public function __construct(private Application $app)
    {
    }

    public function registerCommands(
        string $commandsNamespace,
        string $commandsDirectory
    ): void {
        $this->commandsNamespace = $commandsNamespace;
        $this->commandsDirectory = $commandsDirectory;
        $commandFiles = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($commandsDirectory)
        );
        $files = $this->filterFiles($commandFiles);
        array_walk($files, $this->allowOnlyCommandSubClasses(...));
    }

    public function allowOnlyCommandSubClasses(\SplFileInfo $file): void
    {
        /** @var class-string<object> $className */
        $className = $this->getClassName($file);
        $reflectionClass = new ReflectionClass($className);
        if ($reflectionClass->isSubclassOf(Command::class) &&
            ! $reflectionClass->isAbstract()) {
            new $className($this->app);
        }
    }

    public function getClassName(
        mixed $file,
    ): string {
        return $this->commandsNamespace . str_replace(
                ['/', '.php'],
                ['\\', ''],
                substr(
                    $file->getPathname(),
                    strlen($this->commandsDirectory)
                )
            );
    }

    /**
     * @param RecursiveIteratorIterator<RecursiveDirectoryIterator> $commandFiles
     *
     * @return array<int, \SplFileInfo>
     */
    public function filterFiles(
        RecursiveIteratorIterator $commandFiles,
    ): array {
        $files = [];
        foreach ($commandFiles as $file) {
            $files[] = $file;
        }
        return array_filter(
            $files,
            static fn(\SplFileInfo $file) => $file->isFile()
        );
    }
}
