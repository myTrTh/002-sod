<?php
// $start_time = microtime(true);

// require composer autoload
require_once __DIR__.'/../vendor/autoload.php';

use App\Application;
use App\Core\ServiceProvider;

try {

	$container = new ServiceProvider();

	$app = new Application($container->get());
	$app->run();

} catch (Throwable $t) {
	
	$error = $t->getMessage();
	$error .= '<br>'.$t->getFile().' on line: '.$t->getLine().'<br>';
	echo $error;
	error_log($error, 3, __DIR__.'/../var/log/error.log');	
}