<?php

namespace App\Core;

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Application;

class Database
{
	private $capsule;
	private $connection;

	public function __construct($connection)
	{
		$this->capsule = new Capsule;
		$this->connection = $connection;

		$this->capsule->addConnection($this->connection);
		$this->capsule->setAsGlobal();
		$this->capsule->bootEloquent();
	}

	public function DB()
	{
		return $this->capsule;
	}
}