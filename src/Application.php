<?php

namespace App;

use Symfony\Component\HttpFoundation\Response;
use App\Controller\ErrorController;

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
		try {
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

		} catch (\Exception $e) {
	
			$error = $e->getMessage();
			$error .= '<br>'.$e->getFile().' on line: '.$e->getLine().'<br>';
			error_log($error, 3, __DIR__.'/../var/log/error.log');	

			$controller = new ErrorController($this->container);
			$response = call_user_func_array(array($controller, "error"), ['error' => 404]);
			$response->send();
		}			

	}
}