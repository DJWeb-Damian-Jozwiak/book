<?php

require_once '../vendor/autoload.php';
require_once '../helpers/functions.php';

use DJWeb\Framework\Application;


$app = Application::getInstance();
$app->bind('base_path', dirname(__DIR__));

