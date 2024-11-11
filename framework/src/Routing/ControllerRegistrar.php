<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use DJWeb\Framework\Web\Application;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

class ControllerRegistrar
{
    private string $controllerNamespace;
    private string $controllerDirectory;

    public function __construct(private Application $app)
    {
    }

    public function registerControllers(
        string $controllerNamespace,
        string $controllerDirectory
    ): void {
        $this->controllerNamespace = $controllerNamespace;
        $this->controllerDirectory = $controllerDirectory;
        $commandFiles = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($controllerDirectory)
        );
        $files = $this->filterFiles($commandFiles);
        array_walk($files, $this->allowOnlyCommandSubClasses(...));
    }

    public function getClassName(mixed $file): string
    {
        $substr = substr($file->getPathname(), strlen($this->controllerDirectory));
        return $this->controllerNamespace . str_replace(['/', '.php'], ['\\', ''], $substr);
    }

    public function allowOnlyCommandSubClasses(\SplFileInfo $file): void
    {
        /** @var class-string<object> $className */
        $className = $this->getClassName($file);
        $reflectionClass = new ReflectionClass($className);
        if ($reflectionClass->isSubclassOf(Controller::class) &&
            ! $reflectionClass->isAbstract()) {
            new $className($this->app);
        }
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
            static fn (\SplFileInfo $file) => $file->isFile()
        );
    }
}
