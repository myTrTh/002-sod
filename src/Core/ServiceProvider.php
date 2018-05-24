<?php

namespace App\Core;

use Pimple\Container;
use App\Core\Controller;
use App\Utils\TokenManager;
use App\Utils\UserManager;
use App\Utils\GuestbookManager;
use App\Utils\RoleManager;
use App\Utils\PermissionManager;
use App\Utils\TextMode;
use App\Utils\Dater;
use App\Utils\Assets;
use App\Utils\AdminManager;
use App\Utils\ContentManager;
use App\Utils\VoteManager;
use App\Utils\AuthManager;
use App\Utils\Mailer;
use App\Utils\Upload;

class ServiceProvider 
{
	private $container;
	private $environment;

	public function __construct(string $environment = 'prod')
	{
		if ($environment === 'test')
			$this->environment = 'test/';
		else
			$this->environment = '';

		$this->container = new Container();

		$this->container['config'] = require __DIR__.'/../../config/'.$this->environment.'config.php';
		$this->container['routes'] = require __DIR__.'/../../config/'.$this->environment.'routes.php';
		$this->container['db_connection'] = require __DIR__.'/../../config/'.$this->environment.'db_connection.php';
		$this->container['email_connection'] = require __DIR__.'/../../config/'.$this->environment.'email_connection.php';

		$this->container['router'] = function ($c) {
			return new Router($c['routes']);
		};
		$this->container['session'] = function ($c) {
			return new Session($c['config']);
		};
		$this->container['db'] = function ($c) {
			return new Database($c['db_connection']);
		};
		$this->container['authManager'] = function ($c) {
			return new AuthManager($c);
		};
		$this->container['controller'] = function ($c) {
			return new Controller($c);
		};		
		$this->container['contentManager'] = function ($c) {
			return new ContentManager($c);
		};
		$this->container['guestbookManager'] = function ($c) {
			return new GuestbookManager($c);
		};
		$this->container['assets'] = function ($c) {
			return new Assets($c);
		};		
		$this->container['tokenManager'] = function () {
			return new TokenManager;
		};
		$this->container['upload'] = function ($c) {
			return new Upload($c);
		};
		$this->container['userManager'] = function ($c) {
			return new UserManager($c);
		};
		$this->container['voteManager'] = function ($c) {
			return new VoteManager($c);
		};		
		$this->container['adminManager'] = function ($c) {
			return new AdminManager($c);
		};
		$this->container['textMode'] = function($c) {
			return new TextMode($c);
		};
		$this->container['dater'] = function() {
			return new Dater();
		};
		$this->container['roleManager'] = function ($c) {
			return new RoleManager($c);
		};
		$this->container['permissionManager'] = function ($c) {
			return new PermissionManager($c);
		};		
		$this->container['mailer'] = function ($c) {
			return new Mailer($c);
		};		
	}

	public function get()
	{
		return $this->container;
	}
}