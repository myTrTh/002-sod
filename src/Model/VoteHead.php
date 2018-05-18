<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoteHead extends Eloquent
{
	use SoftDeletes;

	/**
	 * @var array
	 */
	protected $dates = ['deleted_at'];
	protected $table = "vote_head";

	public function author()
	{
		return $this->belongsTo('App\Model\User', 'user_id', 'id');
	}

	public function options()
	{
		return $this->hasMany('App\Model\VoteOption');
	}
}