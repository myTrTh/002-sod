<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\Content;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends Controller
{
	public function list($type, $page)
	{
		$this->container['db'];

		$limit = 10;
		$offset = ($page - 1) * $limit;
		$contents = $this->getContentData($type, $offset, $limit);
		$count = Content::where('type', $type)->orderBy('id', 'desc')->count();

		return $this->render($type.'/list.html.twig', [
			'type' => $type,
			'contents' => $contents,
			'page' => $page,
			'limit' => $limit,
			'count' => $count
		]);
	}

	public function add($type)
	{
		if (!$this->container['userManager']->isPermission('content-control-all') && !$this->container['userManager']->isPermission('content-control-own'))
			return $this->render('error/page403.html.twig', array('errno' => 403));

		$this->container['db'];

		$request = Request::createFromGlobals();

		// default values after submit
		$error = '';
		$lastTitle = trim($request->get('title'));
		$lastArticle = trim($request->get('article'));

		if ($request->get('submit_content_add')) {

			$error = $this->container['contentManager']->add($type, $request);

			if ($error === null)
				return $this->redirectToRoute('content_list', ['type' => $type]);
		}

		return $this->render($type.'/add.html.twig', [
			'type' => $type,
			'error' => $error,
			'lastTitle' => $lastTitle,
			'lastArticle' => $lastArticle
		]);
	}

	public function edit($type, $id)
	{
		$this->container['db'];

		$content = Content::where('id', $id)->first();

		if (!is_object($content) && !($content instanceof Content))
			$error = 'Такого контента не существует';

		if (!$this->container['userManager']->isPermission('content-control-all') && (($this->container['userManager']->isPermission('content-control-own') && $news->user_id == $this->container['userManager']->getUser()['id']) == false))
			return $this->render('error/page403.html.twig', array('errno' => 403));

		// default values after submit
		$error = '';
		$success = '';

		$request = Request::createFromGlobals();

		if ($request->get('submit_content_edit')) {

			$error = $this->container['contentManager']->edit($type, $id, $request);

			if ($error === null)
				return $this->redirectToRoute('content_edit', ['type' => $type, 'id' => $id]);
		}

		if ($request->get('submit_delete_image')) {

			$error = $this->container['contentManager']->deleteImage($type, $id, $request);

			if ($error === null)
				return $this->redirectToRoute('content_edit', ['type' => $type, 'id' => $id]);
		}				

		return $this->render($type.'/edit.html.twig', [
			'type' => $type,
			'error' => $error,
			'content' => $content
		]);
	}

	public function delete($type, $id)
	{
		$this->container['db'];

		// default values after submit
		$error = '';

		$content = Content::where('id', $id)->first();

		if (!is_object($content) && !($content instanceof Content))
			$error = 'Такого контента не существует';

		if (!$this->container['userManager']->isPermission('content-control-all') && (($this->container['userManager']->isPermission('content-control-own') && $news->user_id == $this->container['userManager']->getUser()['id']) == false))
			return $this->render('error/page403.html.twig', array('errno' => 403));

		$request = Request::createFromGlobals();

		if ($request->get('submit_content_delete')) {

			$error = $this->container['contentManager']->delete($type, $id, $request);

			if ($error === null)
				return $this->redirectToRoute('content_list', ['type' => $type, 'id' => $id]);
		}

		return $this->render($type.'/delete.html.twig', [
			'type' => $type,
			'error' => $error,
			'content' => $content
		]);
	}

	private function getContentData(string $type, int $offset, int $limit)
	{
		$this->container['db'];

		if ($type == 'news' || $type == 'stuff')
			return Content::where('type', $type)->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
		else if ($type == 'links')
			return Content::where('type', $type)->orderBy('id', 'asc')->offset($offset)->limit($limit)->get();
		else if ($type == 'band' || $type == 'history')
			return Content::where('type', $type)->get();
		else if ($type == 'albums')
			return Content::where('type', $type)->orderBy('description', 'desc')->get();			
	}
}