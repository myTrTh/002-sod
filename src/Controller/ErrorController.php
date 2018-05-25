<?php

namespace App\Controller;

use App\Core\Controller;

class ErrorController extends Controller
{
	public function error($errno = "404")
	{
		return $this->render('error/page'.$errno.'.html.twig', [
			'errno' => $errno
		]);
	}
}