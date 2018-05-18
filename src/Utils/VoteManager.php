<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use App\Model\VoteHead;
use App\Model\VoteOption;

class VoteManager extends Manager
{
	private $dir = __DIR__.'/../../public/images/content';

	public function add($request)
	{

		if ($error = $this->container['tokenManager']->checkCSRFtoken($request->get('_csrf_token')))
			return $error;

		// prepare data
		$title = trim($request->get('title'));
		$voteOption = $request->get('vote_options');
		for ($i=0; $i < count($voteOption); $i++) {
			$voteOption[$i] = trim($voteOption[$i]);
		}
		if ($lastVoteOption) {		
			$voteOption = array_diff($voteOption, array(""));
			$voteOption = array_unique($voteOption);
		} else {
			return "Должно быть как минимум два варианта ответа.";
		}

		if (count($voteOption) < 2)
			return "Должно быть как минимум два варианта ответа.";		

		if ($error = $this->ifEmptyStringValidate($title, 'Заголовок'))
			return $error;

		$user = $this->container['userManager']->getUser();
		if (!is_object($user) && !($user instanceOf User))
			return 'Вы не авторизированы.';

		$vote = new VoteHead();
		$vote->title = $title;
		$vote->user_id = $user->id;
		$vote->save();

		foreach ($voteOption as $option) {
			$vote_option = new VoteOption();
			$vote_option->title = $option;
		}

		return;
	}

	public function edit($id, $request)
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