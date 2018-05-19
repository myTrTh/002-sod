<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\VoteHead;
use App\Model\VoteUser;
use Symfony\Component\HttpFoundation\Request;

class VoteController extends Controller
{
	public function list($page)
	{
		$this->container['db'];

		$limit = 10;
		$offset = ($page - 1) * $limit;
		$votes = VoteHead::orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
		$count = VoteHead::orderBy('id', 'desc')->count();

		return $this->render('vote/list.html.twig', [
			'votes' => $votes,
			'page' => $page,
			'limit' => $limit,
			'count' => $count
		]);
	}

	public function show($id, $access)
	{
		$this->container['db'];

		$vote = VoteHead::where('id', $id)->first();
		if (!is_object($vote) && !($vote instanceof VoteHead))
			return $this->render('error/page404.html.twig', array('errno' => 404));			

		// if no user
		$user = $this->container['userManager']->getUser();
		if (!is_object($user) && !($user instanceof User)) {
			$vote_access = "open";
			$user_id = 0;
		} else {
			$user_id = $user->id;
		}

		$access_user = VoteUser::where('vote_head_id', $vote->id)->where('user_id', $user_id)->first();
		if (!is_object($access_user) && !($access_user instanceof VoteUser) && $access != 'open') {
			$vote_access = "close";
		} else {
			$vote_access = "open";
		}

		// sort results if open
		if ($vote_access == 'open') {
			// $vote_users = VoteUser::where('vote_head_id', $vote->id)->orderBy('vote_option_id', 'desc')->groupBy('vote_option_id')->get();
			// foreach ($vote_users as $value) {
			// 	echo $value->vote_option_id.'<br>';
			// }
			// $sort_options = [];
			// foreach ($vote->options as $option) {
			// 	$count = count($option->users);
			// 	$sort_options[$count][] = $option;
			// }
			// foreach ($sort_options as $sort) {
			// 	foreach ($sort as $v) {
			// 		print "<pre>";
			// 		print_r($v);
			// 		print "</pre>";
			// 	}
			// }
		}

		$request = Request::createFromGlobals();

		$error = '';

		// all user 
		$count = VoteUser::where('vote_head_id', $vote->id)->count();

		if ($request->get('submit_vote_set')) {

			$error = $this->container['voteManager']->set($id, $request);

			if ($error === null)
				return $this->redirectToRoute('vote_show', ['id' => $id]);

		}

		return $this->render('vote/show.html.twig', [
			'vote' => $vote,
			'access' => $vote_access,
			'error' => $error,
			'count' => $count
		]);		
	}

	public function add()
	{
		if (!$this->container['userManager']->isPermission('content-control-all') && !$this->container['userManager']->isPermission('content-control-own'))
			return $this->render('error/page403.html.twig', array('errno' => 403));

		$this->container['db'];

		$request = Request::createFromGlobals();

		// default values after submit
		$error = '';
		$lastTitle = trim($request->get('title'));
		$lastType = trim($request->get('type'));
		if (empty($lastType))
			$lastType = 1;		
		$lastVoteOptions = $request->get('vote_options');
		for ($i=0; $i < count($lastVoteOptions); $i++) {
			$lastVoteOptions[$i] = trim($lastVoteOptions[$i]);
		}
		if ($lastVoteOptions) {
			$lastVoteOptions = array_diff($lastVoteOptions, array(""));
			$lastVoteOptions = array_unique($lastVoteOptions);
		}

		if ($request->get('submit_vote_add')) {

			$error = $this->container['voteManager']->add($request);

			if ($error === null)
				return $this->redirectToRoute('vote_list');
		}

		return $this->render('vote/add.html.twig', [
			'error' => $error,
			'lastTitle' => $lastTitle,
			'lastType' => $lastType,
			'lastVoteOptions' => $lastVoteOptions
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

		return $this->render('vote/edit.html.twig', [
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

		return $this->render('vote/delete.html.twig', [
			'type' => $type,
			'error' => $error,
			'content' => $content
		]);
	}
}