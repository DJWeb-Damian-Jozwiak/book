<?php

use DJWeb\Framework\Http\Middleware\RedirectMiddleware;
use DJWeb\Framework\Http\Middleware\RequestLoggerMiddleware;
use DJWeb\Framework\Http\Middleware\RouterMiddleware;

return [
    'before_global' => [
        RequestLoggerMiddleware::class,
    ],
    'global' => [
        RouterMiddleware::class,
    ],
    'after_global' => [],
];