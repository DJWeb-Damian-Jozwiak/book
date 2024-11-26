<?php

namespace DJWeb\Framework\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;

class RedirectMiddleware
{
    /**
     * @var callable
     */
    private $exitCallback;

    public function __construct(?callable $exitCallback = null)
    {
        $this->exitCallback = $exitCallback ?? exit(...);
    }
}