<?php
// $start_time = microtime(true);

// require composer autoload
require_once __DIR__.'/../vendor/autoload.php';

use App\Application;
use App\Core\ServiceProvider;

$container = new ServiceProvider();

$app = new Application($container->get());
$app->run();