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

namespace App\Module\Users\Groups\Users\Entity;

use App\Module\Users\Groups\Users\Type\Event\CheckUsersEventUid;
use App\Module\Users\User\Entity\User;
use App\Module\Users\User\Type\Id\UserUid;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

use App\System\Entity\EntityEvent;
use Exception;
use InvalidArgumentException;

/* CheckUsers */

#[ORM\Entity]
#[ORM\Table(name: 'users_group_check_user')]
class CheckUsers
{
    public const TABLE = 'users_group_check_user';
    
    /** ID пользователя */
    #[ORM\Id]
    #[ORM\Column(name: 'user_id', type: UserUid::TYPE)]
    protected UserUid $id;
    
    /** ID События */
    #[ORM\Column(type: CheckUsersEventUid::TYPE, unique: true)]
    protected CheckUsersEventUid $event;
    

    public function __construct(UserUid|User $user) {
        $this->id = $user instanceof User ? $user->getId() : $user;
    }
    
    /**
     * @return UserUid
     */
    public function getId() : UserUid
    {
        return $this->id;
    }
    
    /**
     * @return CheckUsersEventUid
     */
    public function getEvent() : CheckUsersEventUid
    {
        return $this->event;
    }
    

    public function setEvent(CheckUsersEventUid|\App\Module\Users\Groups\Users\Entity\Event\CheckUsersEvent $event) : void
    {
        $this->event = $event instanceof \App\Module\Users\Groups\Users\Entity\Event\CheckUsersEvent ? $event->getId() : $event;
    }
    

    
}