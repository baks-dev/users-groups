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

namespace App\Module\Users\Groups\Users\Entity\Event;

use App\Module\Users\Groups\Group\Type\Prefix\GroupPrefix;
use App\Module\Users\Groups\Users\Entity\CheckUsers;
use App\Module\Users\Groups\Users\Entity\Event\CheckUsersEventInterface;
use App\Module\Users\Groups\Users\Entity\Modify\CheckUserModify;
use App\Module\Users\Groups\Users\Type\Event\CheckUsersEventUid;
use App\Module\Users\User\Type\Id\UserUid;
use App\System\Type\Modify\ModifyAction;
use App\System\Type\Modify\ModifyActionEnum;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

use App\System\Entity\EntityEvent;
use Exception;
use InvalidArgumentException;

/* CheckUsersEvent */

#[ORM\Entity]
#[ORM\Table(name: 'users_group_check_user_event')]
// #[ORM\Index(columns: ['column'])]
class CheckUsersEvent extends EntityEvent
{
    public const TABLE = 'users_group_check_user_event';
    
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CheckUsersEventUid::TYPE)]
    protected CheckUsersEventUid $id;
    
    /** ID События */
    #[ORM\Column(name: 'user_id', type: UserUid::TYPE)]
    protected UserUid $user;
    
    /** Префикс Группы */
    #[ORM\Column(name: 'group_id', type: GroupPrefix::TYPE)]
    protected GroupPrefix $group;
    
    /** Модификатор */
    #[ORM\OneToOne(mappedBy: 'event', targetEntity: CheckUserModify::class, cascade: ['all'])]
    protected CheckUserModify $modify;
    
    public function __construct()
    {
        $this->id = new CheckUsersEventUid();
        $this->modify = new CheckUserModify($this, new ModifyAction(ModifyActionEnum::NEW));
    }
    
    public function __clone()
    {
        $this->id = new CheckUsersEventUid();
    }
    
    /**
     * @return CheckUsersEventUid
     */
    public function getId() : CheckUsersEventUid
    {
        return $this->id;
    }
    

    public function setUser(UserUid|CheckUsers $user) : void
    {
        $this->user = $user instanceof CheckUsers ? $user->getId() : $user;
    }
    
    /**
     * @return UserUid
     */
    public function getUser() : UserUid
    {
        return $this->user;
    }
    
    public function isModifyActionEquals(ModifyActionEnum $action) : bool
    {
        return $this->modify->equals($action);
    }
    
    /**
     * Метод заполняет объект DTO свойствами сущности и возвращает
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof CheckUsersEventInterface)
        {
            return parent::getDto($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    /**
     * Метод присваивает свойствам значения из объекта DTO
     * @throws Exception
     */
    public function setEntity($dto) : mixed
    {
        if($dto instanceof CheckUsersEventInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
}