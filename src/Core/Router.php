<?php

namespace App\Core;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException

class Router 
{
	private $request;
	private $matcher;
	private $requestContext;
	private $routes;

	public function __construct($routes)
	{
		try {
			$this->routes = $routes;
			$this->request = Request::createFromGlobals();
			$this->requestContext = new RequestContext();
			$this->requestContext->fromRequest($this->request);

			$this->matcher = new UrlMatcher($this->routes, $this->requestContext);
		} catch (ResourceNotFoundException $e) {
			echo 'here';
			$response = new Response('Not Found', 404);
			$response->send();
		}
	} 

	public function run(): array
	{
		return $this->matcher->match($this->request->getPathInfo());
	}

	public function getRoutes()
	{
		return $this->routes;
	}

	public function getRequestContext()
	{
		return $this->requestContext;
	}
}