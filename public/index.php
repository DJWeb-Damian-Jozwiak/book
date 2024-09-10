<?php

use DJWeb\Framework\Application;

require_once '../vendor/autoload.php';
try {
    $app = new Application();
    echo $app->handle()->getBody()->getContents();
} catch (\Throwable $e) {
    dump($e->getTrace());
    dd($e->getMessage());
}
