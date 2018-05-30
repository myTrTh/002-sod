<?php

namespace App\Controller;

use App\Core\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
	public function profile()
	{
		$user = $this->container['userManager']->getUser();
		if (!is_object($user) && !($user instanceof User))
			return $this->redirectToRoute('auth_login');

		$error = '';

		$request = Request::createFromGlobals();
		if ($request->get('submit_upload_image')) {
			if ($request->files->get('userfile')) {

				$userManager = $this->container['userManager'];
				$error = $userManager->addImage($request);

				if ($error === null)
					$this->redirectToRoute('user_profile');
			} else {
				$error = "Вы не выбрали изображение";
			}
		}

		if ($request->get('submit_delete_image')) {

			$userManager = $this->container['userManager'];
			$error = $userManager->deleteImage($request);

			if ($error === null)
				$this->redirectToRoute('user_profile');
		}

		return $this->render('user/profile.html.twig', array(
			'error' => $error
		));
	}

	public function changePassword()
	{
		$user = $this->container['userManager']->getUser();
		if (!is_object($user) && !($user instanceof User))
			return $this->redirectToRoute('auth_login');

		$request = Request::createFromGlobals();

		// default values after submit
		$error = '';
		$success = '';

		if ($request->get('submit_change_password')) {
			
			$userManager = $this->container['userManager'];
			$error = $userManager->changePassword($request);

			if ($error === null)
				$success = 'Пароль успешно изменен.';
		}

		return $this->render('user/change_password.html.twig', [
			'error' => $error,
			'success' => $success
		]);
	}
}