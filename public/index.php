<?php

use DJWeb\Framework\Application;

require_once '../vendor/autoload.php';
try {
    $kernel = new Kernel();
    $request = new RequestFactory()->createRequest('GET','/');
    echo $kernel->handle($request)->getBody()->getContents();
} catch (\Throwable $e) {
    dump($e->getTrace());
    dd($e->getMessage());
}
