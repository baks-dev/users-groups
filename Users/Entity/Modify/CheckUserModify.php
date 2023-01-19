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

namespace App\Module\Users\Groups\Users\Entity\Modify;


use App\Module\Users\Groups\Users\Entity\Event\CheckUsersEvent;
use App\Module\Users\User\Entity\User;
use App\Module\Users\User\Type\Id\UserUid;
use App\Module\Users\Groups\Users\Entity\Modify\CheckUserModifyInterface;
use App\System\Entity\EntityEvent;
use App\System\Type\Ip\IpAddress;
use App\System\Type\Modify\ModifyAction;
use App\System\Type\Modify\ModifyActionEnum;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* Модификаторы событий Group */

#[ORM\Entity]
#[ORM\Table(name: 'users_group_check_user_modify')]
#[ORM\Index(columns: ['action'])]
class CheckUserModify extends EntityEvent
{
    public const TABLE = 'users_group_check_user_modify';
    
    /** ID события */
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'modify', targetEntity: CheckUsersEvent::class)]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    protected CheckUsersEvent $event;
    
    /** Модификатор */
    #[ORM\Column(type: ModifyAction::TYPE, nullable: false)]
    protected ModifyAction $action;
    
    /** Дата */
    #[ORM\Column(name: 'mod_date', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $modDate;
    
    /** ID пользователя  */
    #[ORM\Column(name: 'user_id', type: UserUid::TYPE, nullable: true)]
    protected ?UserUid $user = null;
    
    /** Ip адресс */
    #[ORM\Column(name: 'user_ip', type: IpAddress::TYPE, nullable: false)]
    protected IpAddress $ipAddress;
    
    /** User-agent */
    #[ORM\Column(name: 'user_agent', type: Types::TEXT, nullable: false)]
    protected string $userAgent;
    
    
    public function __construct(CheckUsersEvent $event, ModifyAction $modifyAction)
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
        if($dto instanceof CheckUserModifyInterface)
        {
            return parent::getDto($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    public function setEntity($dto) : mixed
    {
        if($dto instanceof CheckUserModifyInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    public function upModifyAgent(IpAddress $ipAddress, string $userAgent) : void
    {
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
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
