<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Eloquent
{
	use SoftDeletes;

	/**
	 * @var array
	 */
	protected $dates = ['deleted_at'];
		
	public function roles()
	{
		return $this->belongsToMany('App\Model\Role');
	}

	public function permissions()
	{
		return $this->belongsToMany('App\Model\Permission');
	}

	public function messages()
	{
		return $this->hasMany('App\Model\Guestbook', 'user_id', 'id');
	}
}