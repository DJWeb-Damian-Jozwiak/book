<?php

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Web\Application;

require_once '../bootstrap/app.php';
/** @var callable $routes */
$routes = require_once '../routes/web.php';
try {
    $app = Application::getInstance();
    $app->withRoutes($routes);
    echo "app env:" . Config::get("app.env") . '<br/>';
    echo $app->handle()->getBody()->getContents();
} catch (\Throwable $e) {
    dump($e->getTrace());
    dd($e->getMessage());
}
