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

namespace BaksDev\Users\Groups\Users\UseCase\Admin\Edit;

use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefix;
use BaksDev\Users\Groups\Users\Entity\Event\CheckUsersEventInterface;
use BaksDev\Users\Groups\Users\Type\Event\CheckUsersEventUid;
use BaksDev\Users\User\Type\Id\UserUid;
use Symfony\Component\Validator\Constraints as Assert;

final class CheckUsersDTO implements CheckUsersEventInterface
{
	/** ID */
	private readonly CheckUsersEventUid $id;
	
	/** ID пользователя */
	#[Assert\Uuid]
	#[Assert\NotBlank]
	private readonly UserUid $user;
	
	/** Префикс Группы */
	#[Assert\NotBlank]
	private GroupPrefix $group;
	
	
	public function getEvent() : ?CheckUsersEventUid
	{
		return $this->id;
	}
	
	
	public function setId(CheckUsersEventUid $id) : void
	{
		$this->id = $id;
	}
	
	/* USER */
	
	/**
	 * @param UserUid $user
	 */
	public function setUser(UserUid $user) : void
	{
		$this->user = $user;
	}
	
	
	/**
	 * @return UserUid
	 */
	public function getUser() : UserUid
	{
		return $this->user;
	}
	
	/* GROUP */
	
	/**
	 * @param GroupPrefix $group
	 */
	public function setGroup(GroupPrefix $group) : void
	{
		$this->group = $group;
	}
	
	
	/**
	 * @return GroupPrefix
	 */
	public function getGroup() : GroupPrefix
	{
		return $this->group;
	}
	
}

