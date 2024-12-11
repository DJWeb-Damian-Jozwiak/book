<?php

use DJWeb\Framework\ErrorHandling\Backtrace;
use DJWeb\Framework\ErrorHandling\Handlers\WebHandler;
use DJWeb\Framework\ErrorHandling\Renderers\WebRenderer;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Web\Application;

require_once '../bootstrap/app.php';
$errorHandler = new WebHandler(
    new WebRenderer(
        debug: true,
        backtrace: new Backtrace()
    ),
    function (string $output) {
        echo $output;
    }
);
$errorHandler->register();
try {
    /** @var callable $routes */
    $routes = require_once '../routes/web.php';
    $app = Application::getInstance();
    $app->session->start();
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
}
