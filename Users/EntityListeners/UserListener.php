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
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class UserListener
{
	//private UserRole $role;
	private KernelInterface $kernel;
	
	private RoleByUserInterface $roleByUser;
	
	
	public function __construct(
		//UserRole $role,
		RoleByUserInterface $roleByUser,
		KernelInterface $kernel,
	)
	{
		$this->kernel = $kernel;
		$this->roleByUser = $roleByUser;
	}
	
	
	public function postLoad(UserInterface $data, LifecycleEventArgs $event) : void
	{
		$cache = new FilesystemAdapter();
		//$cache = new ApcuAdapter();
		
		/* Кешируем результат запроса */
		$role = $cache->get('group-'.$data->getId()->getValue(), function(ItemInterface $item) use ($data) {
			$item->expiresAfter(86400); // 3600 = 1 час / 86400 - сутки
			
			/* По умолчанию Все авторизованные пользователи имеют роль ROLE_USER */
			$roles[] = 'ROLE_USER';
			
			/* Получаем роли согласно группе пользователя */
			$userRoles = $this->roleByUser->get($data->getId());
			
			foreach($userRoles as $userRole)
			{
				if($userRole === null)
				{
					continue;
				}
				$roles[] = $userRole->getUserRole()->getValue();
			}
			
			return $roles;
		});
		
		//        /* Сбрасываем кеш если DEV */
		//        if($this->kernel->getEnvironment() === 'dev')
		//        {
		//            /* Сбрасываем кеш */
		//           $cache->delete('group-'.$data->getId()->getValue());
		//        }
		
		//dd($role);
		
		/* Присваиваем пользователю роли */
		$data->setRole($role);
		
	}
	
}