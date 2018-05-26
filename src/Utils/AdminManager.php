<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use App\Model\User;
use App\Model\Role;

class AdminManager extends Manager
{
	public function setRoles($id, $request)
	{
		if ($error = $this->container['tokenManager']->checkCSRFtoken($request->get('_csrf_token')))
			return $error;

		// prepare data
		$roles = $request->get('roles');
		if (count($roles) == 0)
			return 'У пользователя должна быть минимум одна роль';

		$user = User::where('id', $id)->first();

		if (!is_object($user) && !($user instanceof User))
			return 'Пользователь не найден';

		$r = $user->roles->each(function ($role) use (&$roles, $user) {
			$id = $role['id'];
			
			if (in_array($id, $roles)) {
				$key = array_search($id, $roles);
				unset($roles[$key]);
			} else {
				// if trying delete ROLE_USER, ignore
				if ($id != 1) {
					$role = Role::where('id', $id)->first();
					$user->roles()->detach($role);
					$user->permissions()->detach($role->permissions);
				}
			}
		});

		if (count($roles) > 0) {
			foreach ($roles as $role) {
				$role = Role::where('id', $role)->first();
				$user->roles()->attach($role);
				// no add permissions from role, it's will do handle
				// $user->permissions()->attach($role->permissions);
			}
		}
		return;
	}

	public function setPermissions($id, $request)
	{
		if ($error = $this->container['tokenManager']->checkCSRFtoken($request->get('_csrf_token')))
			return $error;

		// prepare data
		$permissions = $request->get('permissions');

		$user = User::where('id', $id)->first();

		if (!is_object($user) && !($user instanceof User))
			return 'Пользователь не найден';

		$user->permissions()->sync($permissions);

		return;
	}
}