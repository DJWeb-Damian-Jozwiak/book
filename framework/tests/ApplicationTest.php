<?php

namespace Tests;

use DJWeb\Framework\Application;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ApplicationTest extends TestCase
{
    public function testApplicationHandlesRequest(): void
    {
        $app = new Application();
        $response = $app->handle();
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}