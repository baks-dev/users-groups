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

namespace BaksDev\Users\Groups\Group\UseCase\Admin\Delete;

use BaksDev\Users\Groups\Group\Entity\Event\GroupEventInterface;
use BaksDev\Users\Groups\Group\Entity\Trans\GroupTransInterface;
use BaksDev\Users\Groups\Group\Type\Event\GroupEventUid;
use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefix;
use BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\Quota\GroupQuotaDTO;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteGroupDTO implements GroupEventInterface
{
	
	#[Assert\NotBlank]
	#[Assert\Uuid]
	private GroupEventUid $id;
	
	#[Assert\Valid]
	private Modify\ModifyDTO $modify;
	
	
	public function __construct()
	{
		$this->modify = new Modify\ModifyDTO();
	}
	
	
	public function getEvent() : ?GroupEventUid
	{
		return $this->id;
		
	}
	
	
	public function setId(GroupEventUid $id) : void
	{
		$this->id = $id;
	}
	
	
	/* Modify  */
	
	public function getModify() : Modify\ModifyDTO
	{
		return $this->modify;
	}
	
}
