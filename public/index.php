<?php

use DJWeb\Framework\Http\Kernel;
use DJWeb\Framework\Http\RequestFactory;

require_once '../vendor/autoload.php';
try {
    $kernel = new Kernel();
    $request = new RequestFactory()->createRequest('GET','/');
    echo $kernel->handle($request)->getBody()->getContents();
} catch (\Throwable $e) {
    dd($e->getMessage());
}
