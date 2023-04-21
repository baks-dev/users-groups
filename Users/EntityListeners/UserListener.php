<?php
/*
 * Copyright (c) 2022.  Baks.dev <admin@baks.dev>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace BaksDev\Users\Groups\Users\EntityListeners;

use BaksDev\Users\Groups\Users\Repository\RoleByUser\RoleByUserInterface;
use BaksDev\Users\User\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class UserListener
{
	private RoleByUserInterface $roleByUser;
	
	public function __construct(RoleByUserInterface $roleByUser)
	{
		$this->roleByUser = $roleByUser;
	}

	public function postLoad(User $data, LifecycleEventArgs $event) : void
	{
		/* По умолчанию Все авторизованные пользователи имеют роль ROLE_USER */
		$roles[] = 'ROLE_USER';

		$userRoles = $this->roleByUser->fetchAllRoleUser($data->getId());

        array_walk_recursive( $userRoles, static function($value) use (&$roles) {
            if ($value) { $roles[] = $value; }
        }, $roles);

		$data->setRole(array_unique($roles));
	}
}