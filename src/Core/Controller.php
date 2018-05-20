<?php

namespace App\Core;

use App\Core\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\HttpFoundation\Session\Session;

class Controller
{
	protected $container;
	private $view;

	public function __construct($container)
	{
		$this->container = $container;

		$this->view = new View($container);
	}

	public function render(string $template, $data = null)
	{
		return new Response($this->view->render($template, $data));
	}

	// redirect absolute url
	public function redirectUrl(string $url, int $status = 302): RedirectResponse
    {
    	return new RedirectResponse($url, $status);
    	// $redirectResponse->send();
    }

    // redirect to route name
    public function redirectToRoute(string $route, array $parameters = array(), int $status = 302)
    {
		$router = $this->container['router'];
		$generator = new UrlGenerator($router->getRoutes(), $router->getRequestContext());
		$url = $generator->generate($route, $parameters);

		return $this->redirectUrl($url, $status);
    }

 	public function pageKeeper($page)
 	{
 		$session = new Session();
		$session->set('page', $page);
 	}

 	public function getPageNumber(): int
 	{
 		$session = new Session(); 		
 		if ($session->get('page'))
 			return $session->get('page');
 		else
 			return 1;
 	}
}