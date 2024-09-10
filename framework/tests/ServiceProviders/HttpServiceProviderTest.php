<?php

namespace Tests\ServiceProviders;

use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Http\Request;
use DJWeb\Framework\ServiceProviders\HttpServiceProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class HttpServiceProviderTest extends TestCase
{
    private HttpServiceProvider $provider;
    private Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new HttpServiceProvider();
        $this->container = new Container();
    }

    public function testRegister(): void
    {
        $this->provider->register($this->container);

        $this->assertTrue($this->container->has(ServerRequestInterface::class));
        $this->assertInstanceOf(
            Request::class,
            $this->container->get(ServerRequestInterface::class)
        );
    }
}