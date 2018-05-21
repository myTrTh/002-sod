<?php

namespace App;

use Symfony\Component\HttpFoundation\Response;

class Application
{
	private $request;
	private $matcher;
	private $requestContext;
	private $routes;
	protected $container;

	public function __construct($container)
	{
		$this->container = $container;
	}

	public function run()
	{
		$router = $this->container['router'];
		$parameters = $router->run();

		$path = explode('::', $parameters['_controller']);
		list($controller, $action) = $path;

		unset($parameters['_controller'], $parameters['_route']);

		$controller = new $controller($this->container);

		$session = $this->container['session'];
		$session->activate();

		$response = call_user_func_array(array($controller, $action), $parameters);
		$response->send();
	}
}