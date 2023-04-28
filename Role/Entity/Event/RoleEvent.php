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

namespace BaksDev\Users\Groups\Role\Entity\Event;

use BaksDev\Users\Groups\Role\Entity\Event\RoleEventInterface;
use BaksDev\Users\Groups\Role\Entity\Modify\RoleModify;
use BaksDev\Users\Groups\Role\Entity\Role;
use BaksDev\Users\Groups\Role\Entity\Trans\RoleTrans;
use BaksDev\Users\Groups\Role\Entity\Voters\RoleVoter;
use BaksDev\Users\Groups\Role\Type\Event\RoleEventUid;
use BaksDev\Users\Groups\Role\Type\RolePrefix\RolePrefix;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

use BaksDev\Core\Entity\EntityEvent;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* GroupRoleEvent */


#[ORM\Entity]
#[ORM\Table(name: 'users_role_event')]
// #[ORM\Index(columns: ['column'])]
class RoleEvent extends EntityEvent
{
	public const TABLE = 'users_role_event';
	
	/** ID */
    #[Assert\NotBlank]
	#[ORM\Id]
	#[ORM\Column(type: RoleEventUid::TYPE)]
	private RoleEventUid $id;
	
	/** ID Роли */
    #[Assert\NotBlank]
	#[ORM\Column(type: RolePrefix::TYPE)]
	private RolePrefix $role;
	
	/** Настройки локали */
    #[Assert\Valid]
	#[ORM\OneToMany(mappedBy: 'event', targetEntity: RoleTrans::class, cascade: ['all'])]
	private Collection $translate;
	
	/** Правила роли */
    #[Assert\Valid]
	#[ORM\OneToMany(mappedBy: 'event', targetEntity: RoleVoter::class, cascade: ['all'], fetch: 'EXTRA_LAZY')]
	private Collection $voter;
	
	/** Модификатор */
    #[Assert\Valid]
	#[ORM\OneToOne(mappedBy: 'event', targetEntity: RoleModify::class, cascade: ['all'])]
	private RoleModify $modify;
	
	/** Сортировка */
	#[ORM\Column(type: Types::SMALLINT, length: 3, options: ['default' => 500])]
	private int $sort = 500;
	
	/** column */
	//    #[ORM\Column(type: Types::TEXT)]
	//    private ?string $string;
	
	public function __construct()
	{
		$this->id = new RoleEventUid();
		$this->modify = new RoleModify($this, new ModifyAction(ModifyActionEnum::NEW));
	}
	
	
	public function __clone()
	{
		$this->id = new RoleEventUid();
	}
	
	
	/**
	 * @return RoleEventUid
	 */
	public function getId() : RoleEventUid
	{
		return $this->id;
	}
	
	
	/**
	 * @return RolePrefix
	 */
	public function getRole() : RolePrefix
	{
		return $this->role;
	}
	
	
	public function setRole(RolePrefix|Role $role) : void
	{
		$this->role = $role instanceof Role ? $role->getId() : $role;
	}
	
	
	/**
	 * Метод заполняет объект DTO свойствами сущности и возвращает
	 *
	 * @throws Exception
	 */
	public function getDto($dto) : mixed
	{
		if($dto instanceof RoleEventInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	/**
	 * Метод присваивает свойствам значения из объекта DTO
	 *
	 * @throws Exception
	 */
	public function setEntity($dto) : mixed
	{
		if($dto instanceof RoleEventInterface)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	public function isModifyActionEquals(ModifyActionEnum $action) : bool
	{
		return $this->modify->equals($action);
	}
	
}