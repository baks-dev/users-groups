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

namespace BaksDev\Users\Groups\Role\Entity\Modify;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Type\Ip\IpAddress;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use BaksDev\Users\Groups\Role\Entity\Event\RoleEvent;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Модификаторы событий Group */


#[ORM\Entity]
#[ORM\Table(name: 'users_role_modify')]
#[ORM\Index(columns: ['action'])]
class RoleModify extends EntityEvent
{
	public const TABLE = 'users_role_modify';
	
	/** ID события */
    #[Assert\NotBlank]
	#[ORM\Id]
	#[ORM\OneToOne(inversedBy: 'modify', targetEntity: RoleEvent::class)]
	#[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
	private RoleEvent $event;
	
	/** Модификатор */
    #[Assert\NotBlank]
	#[ORM\Column(type: ModifyAction::TYPE, nullable: false)]
	private ModifyAction $action;
	
	/** Дата */
    #[Assert\NotBlank]
	#[ORM\Column(name: 'mod_date', type: Types::DATETIME_IMMUTABLE, nullable: false)]
	private DateTimeImmutable $modDate;
	
	/** ID пользователя  */
	#[ORM\Column(name: 'user_id', type: UserUid::TYPE, nullable: true)]
	private ?UserUid $user = null;
	
	/** Ip адресс */
    #[Assert\NotBlank]
	#[ORM\Column(name: 'user_ip', type: IpAddress::TYPE, nullable: false)]
	private IpAddress $ipAddress;
	
	/** User-agent */
    #[Assert\NotBlank]
	#[ORM\Column(name: 'user_agent', type: Types::TEXT, nullable: false)]
	private string $userAgent;
	
	
	public function __construct(RoleEvent $event, ModifyAction $modifyAction)
	{
		$this->event = $event;
		$this->modDate = new DateTimeImmutable();
		$this->ipAddress = new IpAddress('127.0.0.1');
		$this->userAgent = 'console';
		$this->action = $modifyAction;
	}
	
	
	public function __clone() : void
	{
		$this->modDate = new DateTimeImmutable();
		$this->action = new ModifyAction(ModifyActionEnum::UPDATE);
		$this->ipAddress = new IpAddress('127.0.0.1');
		$this->userAgent = 'console';
	}
	
	
	public function getDto($dto) : mixed
	{
		if($dto instanceof RoleModifyInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	public function setEntity($dto) : mixed
	{
		if($dto instanceof RoleModifyInterface)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	public function upModifyAgent(IpAddress $ipAddress, ?string $userAgent) : void
	{
		$this->ipAddress = $ipAddress;
		$this->userAgent = $userAgent ?: 'console';
		$this->modDate = new DateTimeImmutable();
	}
	
	
	/**
	 * @param UserUid|User|null $user
	 */
	public function setUser(UserUid|User|null $user) : void
	{
		$this->user = $user instanceof User ? $user->getId() : $user;
	}
	
	
	public function equals(ModifyActionEnum $action) : bool
	{
		return $this->action->equals($action);
	}
	
}
