<?php

namespace Tests;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Web\Application;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    protected function tearDown(): void
    {
        Application::withInstance(null);
    }
}