<?php

// composer autoload
require_once '../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Core\Database;
use App\Core\ServiceProvider;

$container = new ServiceProvider();
$container->get()['db'];

Capsule::schema()->create('vote_user', function($table){
	$table->increments('id');
	$table->integer('vote_head_id');
	$table->integer('vote_option_id');
	$table->integer('user_id');
	$table->softDeletes();
	$table->timestamps();
});