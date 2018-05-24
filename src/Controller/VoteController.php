<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\VoteHead;
use App\Model\VoteOption;
use App\Model\VoteUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VoteController extends Controller
{
	public function list($page)
	{
		$this->pageKeeper($page);
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
		} else {

			$access_user = VoteUser::where('vote_head_id', $vote->id)->where('user_id', $user->id)->first();
			if (!is_object($access_user) && !($access_user instanceof VoteUser) && $access != 'open') {
				$vote_access = "close";
			} else {
				$vote_access = "open";
			}
		}

		// sort results if open
		$sort_options = [];
		if ($vote_access == 'open') {

			$options = $vote->options;

			$no_sort = [];
			$no_sort_options = [];
			foreach ($options as $option) {
				$no_sort[$option->id] = count($option->users);
				$no_sort_options[$option->id] = $option;
			}

			arsort($no_sort);

			foreach ($no_sort as $k => $v) {
				$sort_options[] = $no_sort_options[$k];
			}
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

		if ($vote->status === 0)
			$vote_access = "open";

		return $this->render('vote/show.html.twig', [
			'vote' => $vote,
			'sort_options' => $sort_options,
			'access' => $vote_access,
			'error' => $error,
			'count' => $count
		]);
	}

	public function ajax_show()
	{
		$this->container['db'];

		$error = 0;

		$vote = VoteHead::latest()->with('options')->first();
		if (!is_object($vote) && !($vote instanceof VoteHead)) {
			$response = [
				'error' => 1
			];
			return new Response(json_encode($response));
		}

		// // if no user
		$user = $this->container['userManager']->getUser();
		if (!is_object($user) && !($user instanceof User)) {
			$vote_access = "open";
		} else {

			$access_user = VoteUser::where('vote_head_id', $vote->id)->where('user_id', $user->id)->first();
			if (!is_object($access_user) && !($access_user instanceof VoteUser)) {
				$vote_access = "close";
			} else {
				$vote_access = "open";
			}
		}

		// // sort results if open
		$sort_options = [];
		if ($vote_access == 'open') {

			$options = $vote->options;

			$no_sort = [];
			$no_sort_options = [];
			foreach ($options as $option) {
				$no_sort[$option->id] = count($option->users);
				$no_sort_options[$option->id] = $option;
			}

			arsort($no_sort);

			foreach ($no_sort as $k => $v) {
				$sort_options[] = $no_sort_options[$k];
			}
		}

		$request = Request::createFromGlobals();

		$error_message = '';

		// all user 
		$count = VoteUser::where('vote_head_id', $vote->id)->count();

		if ($vote->status === 0)
			$vote_access = "open";

		$response = [
			'error' => $error,
			'vote' => $vote,
			'sort_options' => $sort_options,
			'access' => $vote_access,
			'error' => $error,
			'count' => $count
		];

		return new Response(json_encode($response));
	}

	public function ajax_send()
	{
		$this->container['db'];

		$request = Request::createFromGlobals();
		$vote = VoteHead::latest()->first();
		if (!is_object($vote) && !($vote instanceof VoteHead))
			return new Response(json_encode('Произошла ошибка.'));

		$error = $this->container['voteManager']->set($vote->id, $request);

		return new Response(json_encode($error));
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

	public function edit($id)
	{
		$this->container['db'];

		$vote = VoteHead::where('id', $id)->first();
		if (!is_object($vote) && !($vote instanceof VoteHead))
			return $this->render('error/page404.html.twig', array('errno' => 404));

		if (!$this->container['userManager']->isPermission('content-control-all') && (($this->container['userManager']->isPermission('content-control-own') && $vote->user_id == $this->container['userManager']->getUser()['id']) == false))
			return $this->render('error/page403.html.twig', array('errno' => 403));

		// default values after submit
		$error = '';
		$success = '';

		$request = Request::createFromGlobals();

		if ($request->get('submit_vote_edit')) {

			$error = $this->container['voteManager']->edit($id, $request);

			if ($error === null)
				return $this->redirectToRoute('vote_list');
		}

		return $this->render('vote/edit.html.twig', [
			'error' => $error,
			'vote' => $vote
		]);
	}

	public function delete($id)
	{
		$this->container['db'];

		// default values after submit
		$error = '';

		$vote = VoteHead::where('id', $id)->first();

		if (!is_object($vote) && !($vote instanceof VoteHead))
			$error = 'Такого опроса не существует';

		if (!$this->container['userManager']->isPermission('content-control-all') && (($this->container['userManager']->isPermission('content-control-own') && $vote->user_id == $this->container['userManager']->getUser()['id']) == false))
			return $this->render('error/page403.html.twig', array('errno' => 403));

		$request = Request::createFromGlobals();

		if ($request->get('submit_vote_delete')) {

			$error = $this->container['voteManager']->delete($id, $request);

			if ($error === null)
				return $this->redirectToRoute('vote_list', ['id' => $id]);
		}

		return $this->render('vote/delete.html.twig', [
			'error' => $error,
			'vote' => $vote
		]);
	}
}