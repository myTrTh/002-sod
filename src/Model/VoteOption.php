<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoteOption extends Eloquent
{
	use SoftDeletes;

	/**
	 * @var array
	 */
	protected $dates = ['deleted_at'];
	protected $table = "vote_option";

	public function users()
	{
		return $this->hasMany('App\Model\VoteUser');
	}
}