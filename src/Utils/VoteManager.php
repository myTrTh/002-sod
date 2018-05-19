<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use App\Model\VoteHead;
use App\Model\VoteOption;
use App\Model\VoteUser;

class VoteManager extends Manager
{
	private $dir = __DIR__.'/../../public/images/content';

	public function add($request)
	{
		$this->container['db'];

		if ($error = $this->container['tokenManager']->checkCSRFtoken($request->get('_csrf_token')))
			return $error;

		$title = trim($request->get('title'));
		$type = trim($request->get('type'));
		if (empty($type))
			$type = 1;

		if ($error = $this->ifEmptyStringValidate($title, 'Заголовок'))
			return $error;

		$voteOption = $request->get('vote_options');
		for ($i=0; $i < count($voteOption); $i++) {
			$voteOption[$i] = trim($voteOption[$i]);
		}
		if ($voteOption) {
			$voteOption = array_diff($voteOption, array(""));
			$voteOption = array_unique($voteOption);
		}

		if (count($voteOption) < 2)
			return "Должно быть как минимум два варианта ответа.";		


		$user = $this->container['userManager']->getUser();
		if (!is_object($user) && !($user instanceOf User))
			return 'Вы не авторизированы.';

		$vote = new VoteHead();
		$vote->title = $title;
		$vote->user_id = $user->id;
		$vote->status = 1;
		$vote->type = $type;
		$vote->save();

		foreach ($voteOption as $option) {
			$vote_option = new VoteOption();
			$vote_option->title = $option;
			$vote_option->vote_head_id = $vote->id;
			$vote_option->save();
		}

		return;
	}

	public function set($id, $request)
	{
		$this->container['db'];

		if ($error = $this->container['tokenManager']->checkCSRFtoken($request->get('_csrf_token')))
			return $error;

		$user = $this->container['userManager']->getUser();
		if (!is_object($user) && !($user instanceOf User))
			return 'Вы не авторизированы.';

		$vote = VoteHead::where('id', $id)->first();
		if (!is_object($vote) && !($vote instanceOf VoteHead))
			return 'Опрос не найден.';

		$vote_user = VoteUser::where('vote_head_id', $vote->id)->where('user_id', $user->id)->first();
		if (is_object($vote_user) && ($vote_user instanceof VoteUser))
			return "Вы уже голосовали";

		$option = $request->get('vote_options');

		if (count($option) == 0) {
			return "Выберите вариант ответа";
		} else if ($vote->type != 0 && (count($option) > $vote->type)) {
			return "Количество возможных ответов не должно превышать: ".$vote->type;
		}

		foreach ($option as $value) {
			$vote_user = new VoteUser();
			$vote_user->vote_head_id = $vote->id;			
			$vote_user->vote_option_id = $value;
			$vote_user->user_id = $user->id;
			$vote_user->save();
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