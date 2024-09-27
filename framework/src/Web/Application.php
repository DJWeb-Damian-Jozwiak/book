<?php

declare(strict_types=1);

namespace DJWeb\Framework\Web;

use DJWeb\Framework\Http\Kernel;
use DJWeb\Framework\ServiceProviders\HttpServiceProvider;
use DJWeb\Framework\ServiceProviders\RouterServiceProvider;
use DJWeb\Framework\ServiceProviders\SchemaServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application extends \DJWeb\Framework\Base\Application
{
    private Kernel $kernel;

    protected function __construct()
    {
        parent::__construct();
        $this->registerServiceProvider(new HttpServiceProvider());
        $this->registerServiceProvider(new RouterServiceProvider());
        $this->registerServiceProvider(new SchemaServiceProvider());
        $this->kernel = new Kernel($this);
    }

    public function handle(): ResponseInterface
    {
        $request = $this->get(ServerRequestInterface::class);
        return $this->kernel->handle($request);
    }

    public function withRoutes(callable $callback): void
    {
        $this->kernel->withRoutes($callback);
    }
}
