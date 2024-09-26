<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

class CommandRegistrar
{
    private static $loadedClasses = [];
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
        $files = array_filter($files, $this->classDoesNotExist(...));
        array_walk($files, $this->registerClassAsLoaded(...));
        array_walk($files, $this->allowOnlyCommandSubClasses(...));
//        $files = array_filter($files, function ($file) {
//            /** @var class-string<object> $className */
//            $className = $this->getClassName($file);
//            $reflectionClass = new ReflectionClass($className);
//            return $reflectionClass->isSubclassOf(Command::class) &&
//                ! $reflectionClass->isAbstract();
//        });
//
//        foreach ($files as $file) {
//            $className = $this->getClassName(
//                $file,
//            );
//            new $className($this->app);
//        }
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

    private function classDoesNotExist(\SplFileInfo $file): bool
    {
        $className = $this->getClassName($file);
        return ! isset(self::$loadedClasses[$className]);
    }

    private function registerClassAsLoaded(\SplFileInfo $file): void
    {
        $className = $this->getClassName($file);
        if (file_exists($file->getPathname())) {
            $exists = class_exists($className, autoload: false);
            //dump($exists);
            if (! $exists) {
                self::$loadedClasses[$className] = true;
            }
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
//
//    private function classExistsOrCanBeLoaded(
//        string $className,
//        \SplFileInfo $file
//    ): bool {
//        if (isset(self::$loadedClasses[$className])) {
//            return self::$loadedClasses[$className];
//        }
//
//        $filePath = $this->getClassFilePath($className);
//        if (file_exists($file->getPathname())) {
//            include_once $file->getPathname();
//            $exists = class_exists($className);
//            self::$loadedClasses[$className] = $exists;
//            return $exists;
//        }
//
//        self::$loadedClasses[$className] = false;
//        return false;
//    }
//
//    private function getClassFilePath(string $className): string
//    {
//        return str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
//    }
}
