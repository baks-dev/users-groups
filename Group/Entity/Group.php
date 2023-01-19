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

namespace App\Module\Users\Groups\Group\Entity;

use App\Module\Users\Groups\Group\Entity\Event\GroupEvent;
use App\Module\Users\Groups\Group\Type\Event\GroupEventUid;
use App\Module\Users\Groups\Group\Type\Prefix\GroupPrefix;
use Doctrine\ORM\Mapping as ORM;

//use App\Module\Users\Groups\Type\Group\Id\GroupUid;

/* Group */

#[ORM\Entity]
#[ORM\Table(name: 'users_group')]
class Group
{
    
    public const TABLE = 'users_group';
    
    /** Префикс */
    #[ORM\Id]
    #[ORM\Column(type: GroupPrefix::TYPE, nullable: false)]
    private GroupPrefix $id;
    
    /** ID События */
    #[ORM\Column(type: GroupEventUid::TYPE, unique: true)]
    private GroupEventUid $event;
    

    public function __construct(GroupPrefix $id)
    {
        $this->id = $id;
    }
    
    /**
     * @return GroupPrefix
     */
    public function getId() : GroupPrefix
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getUserRole() : string
    {
        return $this->id;
    }
    
//    /**
//     * @param GroupPrefix $id
//     */
//    public function setId(GroupPrefix $id) : void
//    {
//        $this->id = $id;
//    }
    
    /**
     * @return GroupEventUid
     */
    public function getEvent() : GroupEventUid
    {
        return $this->event;
    }
    
    /**
     * @param GroupEventUid|GroupEvent $event
     */
    public function setEvent(GroupEventUid|GroupEvent $event) : void
    {
        $this->event = $event instanceof GroupEvent ? $event->getId() : $event;
    }
    
}