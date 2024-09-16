<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

class CommandRegistrar
{
    public function __construct(private Application $app)
    {
    }

    public function registerCommands(
        string $commandsNamespace,
        string $commandsDirectory
    ): void {
        $commandFiles = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($commandsDirectory)
        );
        $files = $this->filterFiles($commandFiles, $commandsNamespace, $commandsDirectory);


        foreach ($files as $file) {
            $className = $this->getClassName($commandsNamespace, $file, $commandsDirectory);
            $reflectionClass = new ReflectionClass($className);

            if ($reflectionClass->isSubclassOf(Command::class) && !$reflectionClass->isAbstract()) {
                new $className($this->app);
            }
        }
    }

    public function getClassName(string $commandsNamespace, mixed $file, string $commandsDirectory): string
    {
        return $commandsNamespace . str_replace(
                ['/', '.php'],
                ['\\', ''],
                substr(
                    $file->getPathname(),
                    strlen($commandsDirectory)
                )
            );
    }

    /**
     * @param RecursiveIteratorIterator $commandFiles
     * @param string $commandsNamespace
     * @param string $commandsDirectory
     *
     * @return array<int, \SplFileInfo>
     */
    public function filterFiles(
        RecursiveIteratorIterator $commandFiles,
        string $commandsNamespace,
        string $commandsDirectory
    ): array {
        $files = [];
        foreach ($commandFiles as $file) {
            $files[] = $file;
        }
        $files = array_filter($files, static fn(\SplFileInfo $file) => $file->isFile());
        return array_filter(
            $files,
            fn(
                \SplFileInfo $file
            ) => class_exists(
                $this->getClassName($commandsNamespace, $file, $commandsDirectory)
            )
        );
    }
}
