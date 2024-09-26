<?php

namespace Tests\Helpers;

use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Console\Attributes\CommandArgument;
use DJWeb\Framework\Console\Attributes\CommandOption;
use DJWeb\Framework\Console\Command;

#[AsCommand(name: 'test')]
class TestCommand extends Command
{
    #[CommandArgument(name: 'testArgument')]
    private $testArgument;

    #[CommandOption(name: 'option', value: 'default_value')]
    private $option;

    #[CommandOption(name: 'required_option', required: true)]
    private $requiredOption;

    public function getTestArgument()
    {
        return $this->testArgument;
    }

    public function getTestOption()
    {
        return $this->option;
    }

    public function getRequiredOption()
    {
        return $this->requiredOption;
    }

    public function run(): int
    {
        return 0;
    }
}