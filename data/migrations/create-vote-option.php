<?php

// composer autoload
require_once '../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Core\Database;
use App\Core\ServiceProvider;

$container = new ServiceProvider();
$container->get()['db'];

Capsule::schema()->create('vote_option', function($table){
	$table->increments('id');
	$table->string('title');
	$table->integer('vote_head_id');
	$table->softDeletes();
	$table->timestamps();
});