<?php

namespace App\Core;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extension_Debug;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Routing\Generator\UrlGenerator;
use App\Utils\TwigExtension;

class View
{
	private $twig;
	protected $container;

	public function __construct($container)
	{
		$this->container = $container;

		// configure Twig
		$loader = new Twig_Loader_Filesystem(__DIR__.'/../../templates/');
		$this->twig = new Twig_Environment($loader, array(
	   		'cache' => __DIR__.'/../../var/cache/twig/compilation_cache',
	   		'auto_reload' => true,
	   		'debug' => false,
		));

		$router = $container['router'];

		$this->twig->addExtension(new RoutingExtension(new UrlGenerator($router->getRoutes(), $router->getRequestContext())));
		$this->twig->addExtension(new Twig_Extension_Debug());
		$this->twig->addExtension(new TwigExtension($this->container));
	}

	public function render(string $template, $data = null)
	{
		if ($data)
			return $this->twig->render($template, $data);
		else
			return $this->twig->render($template);
	}
}