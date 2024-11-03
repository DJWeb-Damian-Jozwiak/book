<?php

require_once '../vendor/autoload.php';
try {
    dd($_SERVER);
//    $kernel = new Kernel();
//    $request = new RequestFactory()->createRequest('GET','/');
//    echo $kernel->handle($request)->getBody()->getContents();
} catch (\Throwable $e) {
    dd($e->getMessage());
}
