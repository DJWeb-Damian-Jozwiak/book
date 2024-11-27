<?php

use DJWeb\Framework\Http\Middleware\InertiaMiddleware;
use DJWeb\Framework\Http\Middleware\RedirectMiddleware;
use DJWeb\Framework\Http\Middleware\RequestLoggerMiddleware;
use DJWeb\Framework\Http\Middleware\RouterMiddleware;
use DJWeb\Framework\Http\Middleware\ValidationErrorMiddleware;

return [
    'before_global' => [
        RequestLoggerMiddleware::class,
        InertiaMiddleware::class,
    ],
    'global' => [
        RouterMiddleware::class,
    ],
    'after_global' => [
        ValidationErrorMiddleware::class,
    ],
];