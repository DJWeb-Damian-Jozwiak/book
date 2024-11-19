<?php

use DJWeb\Framework\Http\Middleware\InertiaMiddleware;
use DJWeb\Framework\Http\Middleware\RequestLoggerMiddleware;
use DJWeb\Framework\Http\Middleware\RouterMiddleware;

return [
    'before_global' => [
        RequestLoggerMiddleware::class,
        InertiaMiddleware::class,
    ],
    'global' => [
        RouterMiddleware::class
    ],
    'after_global' => [],
];