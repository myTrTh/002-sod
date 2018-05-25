<?php

namespace App;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
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

		} catch (ResourceNotFoundException $e) {
	
			$error = $e->getMessage();
			$error .= '<br>'.$e->getFile().' on line: '.$e->getLine().'<br>';
			// echo $error;
			error_log($error, 3, __DIR__.'/../var/log/error.log');	

			$session = $this->container['session'];
			$session->activate();

			$controller = new ErrorController($this->container);
			$response = call_user_func_array(array($controller, "error"), ['error' => 404]);
			$response->send();

		} catch (Throwable $t) {

			$error = $t->getMessage();
			$error .= '<br>'.$t->getFile().' on line: '.$t->getLine().'<br>';
			// echo $error;
			error_log($error, 3, __DIR__.'/../var/log/error.log');	

			$session = $this->container['session'];
			$session->activate();

			$controller = new ErrorController($this->container);
			$response = call_user_func_array(array($controller, "error"), ['error' => 500]);
			$response->send();
		}
	}
}