<?php

use DJWeb\Framework\Storage\Session\Handlers\FileSessionHandler;

return [
    'path' => sys_get_temp_dir() . '/sessions',
    'handler' => \DJWeb\Framework\Storage\Session\Handlers\DatabaseSessionHandler::class,
    'cookie_params' => [
        'lifetime' => 7200,
        'path' => null,
        'domain' => null,
        'secure' =>  isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]
];