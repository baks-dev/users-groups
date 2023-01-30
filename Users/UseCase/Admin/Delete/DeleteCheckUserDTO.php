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

namespace BaksDev\Users\Groups\Users\UseCase\Admin\Delete;

use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefix;
use BaksDev\Users\Groups\Users\Entity\CheckUserInterface;
use BaksDev\Users\Groups\Users\Entity\Event\CheckUsersEventInterface;
use BaksDev\Users\Groups\Users\Entity\Modify\CheckUserModifyInterface;
use BaksDev\Users\Groups\Users\Type\Event\CheckUsersEventUid;
use BaksDev\Users\User\Type\Id\UserUid;
use Symfony\Component\Validator\Constraints as Assert;

final class DeleteCheckUserDTO implements CheckUsersEventInterface
{
	/** ID */
	private readonly CheckUsersEventUid $id;
	
	private \BaksDev\Users\Groups\Users\UseCase\Admin\Delete\Modify\ModifyDTO $modify;
	
	
	public function __construct()
	{
		$this->modify = new \BaksDev\Users\Groups\Users\UseCase\Admin\Delete\Modify\ModifyDTO();
	}
	
	
	/**
	 * @param CheckUsersEventUid $id
	 */
	public function setId(CheckUsersEventUid $id) : void
	{
		$this->id = $id;
	}
	
	
	/**
	 * @return CheckUsersEventUid
	 */
	public function getEvent() : CheckUsersEventUid
	{
		return $this->id;
	}
	
	
	/* MODIFY  */
	
	/**
	 * @return \BaksDev\Users\Groups\Users\UseCase\Admin\Delete\Modify\ModifyDTO
	 */
	public function getModify() : \BaksDev\Users\Groups\Users\UseCase\Admin\Delete\Modify\ModifyDTO
	{
		return $this->modify;
	}
	
	
	/** Метод для инициализации и маппинга сущности на DTO в коллекции  */
	public function getModifyClass() : CheckUserModifyInterface
	{
		return new \BaksDev\Users\Groups\Users\UseCase\Admin\Delete\Modify\ModifyDTO();
	}
	
}

