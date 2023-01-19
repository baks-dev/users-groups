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

namespace BaksDev\Users\Groups\Group\Entity\CheckRole;

use BaksDev\Users\Groups\Group\Entity\Event\GroupEvent;
use BaksDev\Users\Groups\Group\Entity\CheckRole\CheckRoleInterface;
use BaksDev\Users\Groups\Group\Type\Check\GroupCheckUid;
use BaksDev\Users\Groups\Role\Type\RolePrefix\RolePrefix;
//use BaksDev\Core\Entity\EntityEvent;

use BaksDev\Core\Entity\EntityEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/** Отмеченные роли для группы */

#[ORM\Entity]
#[ORM\Table(name: 'users_group_check_role')]
#[ORM\UniqueConstraint(columns: ['event', 'role'])]
class CheckRole extends EntityEvent
{
    public const TABLE = 'users_group_check_role';
    
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: GroupCheckUid::TYPE)]
    protected GroupCheckUid $id;
    
    /** Связь на событие группы */
    #[ORM\ManyToOne(targetEntity: GroupEvent::class, inversedBy: "role")]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: "id")]
    protected GroupEvent $event;
    
    /** Префикс роли */
    #[ORM\Column(type: RolePrefix::TYPE)]
    protected RolePrefix $role;
    
    /** Правила роли */
    #[ORM\OneToMany(mappedBy: 'check', targetEntity: \BaksDev\Users\Groups\Group\Entity\CheckRole\CheckVoter\CheckVoter::class, cascade: ['all'])]
    protected Collection $voter;
    
    public function __construct(GroupEvent $event)
    {
        $this->id = new GroupCheckUid();
        $this->event = $event;
        //$this->voter = new ArrayCollection();
    }
    
    public function __clone()
    {
        $this->id = new GroupCheckUid();
    }
    
    /**
     * @return GroupCheckUid
     */
    public function getId() : GroupCheckUid
    {
        return $this->id;
    }
    
    /**
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof CheckRoleInterface)
        {
            return parent::getDto($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    /**
     * @throws Exception
     */
    public function setEntity($dto) : mixed
    {
        if($dto instanceof CheckRoleInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    public function getUserRole() : RolePrefix
    {
        return $this->role;
    }
}
