<?php

namespace App\Utils;

class Assets
{
	private $container;

	public function __construct($container)
	{
		$this->container = $container;
	}

	public function assets()
	{
		return $this->container['config']['assets'];
	}
}