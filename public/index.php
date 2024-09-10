<?php

use DJWeb\Framework\Http\Kernel;

require_once '../vendor/autoload.php';
try {
    $kernel = new Kernel();
    echo $kernel->handle(\DJWeb\Framework\Http\Request::createFromSuperglobals())->getBody()->getContents();
} catch (\Throwable $e) {
    dd($e->getMessage());
}
