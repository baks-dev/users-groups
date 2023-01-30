<?php
/*
 * Copyright (c) 2023.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Users\Groups\Group\DataFixtures\Security\Group\CheckUser;

use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefix;
use BaksDev\Users\Groups\Users\Entity\CheckUserInterface;
use BaksDev\Users\Groups\Users\Entity\Event\CheckUsersEventInterface;
use BaksDev\Users\Groups\Users\Type\Event\CheckUsersEventUid;
use BaksDev\Users\User\Type\Id\UserUid;
use Symfony\Component\Validator\Constraints as Assert;

final class CheckUsersDTO implements CheckUsersEventInterface
{
	/** ID */
	private ?CheckUsersEventUid $id = null;
	
	/** ID пользователя */
	#[Assert\Uuid]
	#[Assert\NotBlank]
	private UserUid $user;
	
	/** Префикс Группы */
	#[Assert\NotBlank]
	private GroupPrefix $group;
	
	
	/**
	 * @param UserUid $user
	 * @param GroupPrefix $group
	 */
	public function __construct(UserUid $user, GroupPrefix $group)
	{
		$this->user = $user;
		$this->group = $group;
	}
	
	
	public function getEvent() : ?CheckUsersEventUid
	{
		return $this->id;
	}
	
	
	public function setId(CheckUsersEventUid $id) : void
	{
		$this->id = $id;
	}
	
	
	/**
	 * @return UserUid
	 */
	public function getUser() : UserUid
	{
		return $this->user;
	}
	
	
	/**
	 * @return GroupPrefix
	 */
	public function getGroup() : GroupPrefix
	{
		return $this->group;
	}
	
}

