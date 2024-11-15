<?php

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Web\Application;

require_once '../bootstrap/app.php';

try {
    /** @var callable $routes */
    $routes = require_once '../routes/web.php';
    $app = Application::getInstance();
    $app->loadRoutes(
        '\\App\\Controllers\\',
        __DIR__ . '/../app/Controllers/'
    );
    $app->withRoutes($routes);
    echo $app->handle()->getBody()->getContents();
} catch (\DJWeb\Framework\Exceptions\Validation\ValidationError $error) {
    echo json_encode([
        'status' => $error->getMessage(),
        'errors' => $error->validationErrors
    ]);
} catch (\Throwable $e) {
    dump($e->getTrace());
    dd($e->getMessage());
}
