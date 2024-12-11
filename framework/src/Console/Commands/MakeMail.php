<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Commands;

use DJWeb\Framework\Console\Attributes\AsCommand;

#[AsCommand(name: 'make:mail')]
class MakeMail extends MakeCommand
{
    protected function getDefaultNamespace(): string
    {
        return $this->rootNamespace() . 'Mail';
    }

    protected function getPath(string $name): string
    {
        $name = str_replace('\\', '/', $name);
        return $this->container->getBinding('app.mail_path') . '/' . $name;
    }

    protected function getStub(): string
    {
        $dir = dirname(__DIR__, 3);
        return $dir . '/stubs/mail.stub';
    }

    protected function buildClass(string $name): string
    {
        $stub = parent::buildClass($name);
        return str_replace('DummyName', strtolower($this->name), $stub);
    }
}
