<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Users\Groups\Role\Entity;

use BaksDev\Users\Groups\Role\Entity\Event\RoleEvent;
use BaksDev\Users\Groups\Role\Type\Event\RoleEventUid;
use BaksDev\Users\Groups\Role\Type\RolePrefix\RolePrefix;
use Doctrine\ORM\Mapping as ORM;
use BaksDev\Core\Entity\EntityEvent;

/* Role */


#[ORM\Entity]
#[ORM\Table(name: 'users_role')]
class Role
{
	public const TABLE = 'users_role';
	
	/** ID */
	#[ORM\Id]
	#[ORM\Column(type: RolePrefix::TYPE)]
	protected RolePrefix $id;
	
	/** ID События */
	#[ORM\Column(type: RoleEventUid::TYPE, unique: true)]
	protected RoleEventUid $event;
	
	
	public function __construct(RolePrefix $id)
	{
		$this->id = $id;
	}
	
	
	/**
	 * @return RolePrefix
	 */
	public function getId() : RolePrefix
	{
		return $this->id;
	}
	
	
	/**
	 * @return RoleEventUid
	 */
	public function getEvent() : RoleEventUid
	{
		return $this->event;
	}
	
	
	public function setEvent(RoleEventUid|RoleEvent $event) : void
	{
		$this->event = $event instanceof RoleEvent ? $event->getId() : $event;
	}
	
}