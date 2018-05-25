<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use App\Model\Content;

class ContentManager extends Manager
{
	private $dir = '/images/content';

	public function add($type, $request)
	{
		// prepare data
		$title = trim($request->get('title'));
		$article = trim($request->get('article'));		
		$description = trim($request->get('description')) ?? '';

		if ($error = $this->container['tokenManager']->checkCSRFtoken($request->get('_csrf_token')))
			return $error;

		if ($error = $this->ifEmptyStringValidate($title, 'Заголовок'))
			return $error;

		if ($error = $this->ifEmptyStringValidate($article, 'Текст'))
			return $error;

		$user = $this->container['userManager']->getUser();
		if (!is_object($user) && !($user instanceOf User))
			return 'Вы не авторизированы.';

		$uploadedFile = $request->files->get('userfile');

		$content = new Content();

		if ($uploadedFile) {
			$upload = $this->container['upload'];
			$file = $upload->upload($uploadedFile, $this->dir.'/'.$type, 200000);
			if ($file[0]) {
				$content->image = $file[1];
			} else {
				return $file[1];
			}
		}

		$content->type = $type;
		$content->title = $title;
		$content->article = $article;
		$content->description = $description;
		$content->user_id = $user->id;
		$content->save();

		return;
	}

	public function edit($type, $id, $request)
	{
		// prepare data
		$id = (int) $id;
		$title = trim($request->get('title'));
		$article = trim($request->get('article'));
		$description = trim($request->get('description')) ?? '';

		if ($error = $this->container['tokenManager']->checkCSRFtoken($request->get('_csrf_token')))
			return $error;

		if ($error = $this->ifEmptyStringValidate($title, 'Заголовок'))
			return $error;

		if ($error = $this->ifEmptyStringValidate($article, 'Текст'))
			return $error;


		$content = Content::where('id', $id)->first();

		if (!is_object($content) && !($content instanceof Content))
			return 'Такого контента не существует';

		$uploadedFile = $request->files->get('userfile');

		if ($uploadedFile) {
			$upload = $this->container['upload'];
			$file = $upload->upload($uploadedFile, $this->dir.'/'.$type, 150000);
			if ($file[0]) {
				$oldimage = $content->image;
				if ($oldimage)
					$upload->delete($oldimage, $this->dir.'/'.$type);

				$content->image = $file[1];
			} else {
				return $file[1];
			}
		}

		$content->title = $title;
		$content->article = $article;
		$content->description = $description;
		$content->save();

		return;
	}

	public function delete($type, $id, $request)
	{
		// prepare data
		$id = (int) $id;

		if ($error = $this->container['tokenManager']->checkCSRFtoken($request->get('_csrf_token')))
			return $error;

		$content = Content::where('id', $id)->first();

		if (!is_object($content) && !($content instanceof Content))
			return 'Такого контента не существует';

		$content->delete();

		return;
	}

	public function deleteImage($type, $id, $request)
	{
		if ($error = $this->container['tokenManager']->checkCSRFtoken($request->get('_csrf_token')))
			return $error;

		$upload = $this->container['upload'];

		$uploadedFile = $request->files->get('userfile');

		$content = Content::where('id', $id)->first();

		if (!is_object($content) && !($content instanceof Content))
			return 'Такого контента нет';
		
		$image = $content->image;
		if ($image)
			$upload->delete($image, $this->dir.'/'.$type);
		else
			return 'Изображение не установлено';

		$content->image = null;
		$content->save();

		return;
	}		
}