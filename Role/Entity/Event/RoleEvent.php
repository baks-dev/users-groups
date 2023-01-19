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

namespace App\Module\Users\Groups\Role\Entity\Event;

use App\Module\Users\Groups\Role\Entity\Event\RoleEventInterface;
use App\Module\Users\Groups\Role\Entity\Modify\RoleModify;
use App\Module\Users\Groups\Role\Entity\Role;
use App\Module\Users\Groups\Role\Entity\Trans\RoleTrans;
use App\Module\Users\Groups\Role\Entity\Voters\RoleVoter;
use App\Module\Users\Groups\Role\Type\Event\RoleEventUid;
use App\Module\Users\Groups\Role\Type\RolePrefix\RolePrefix;
use App\System\Type\Modify\ModifyAction;
use App\System\Type\Modify\ModifyActionEnum;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

use App\System\Entity\EntityEvent;
use Exception;
use InvalidArgumentException;

/* GroupRoleEvent */

#[ORM\Entity]
#[ORM\Table(name: 'users_role_event')]
// #[ORM\Index(columns: ['column'])]
class RoleEvent extends EntityEvent
{
    public const TABLE = 'users_role_event';
    
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: RoleEventUid::TYPE)]
    protected RoleEventUid $id;
    
    /** ID Роли */
    #[ORM\Column(type: RolePrefix::TYPE)]
    protected RolePrefix $role;
    
    /** Настройки локали */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: RoleTrans::class, cascade: ['all'])]
    protected Collection $translate;
    
    /** Правила роли */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: RoleVoter::class, cascade: ['all'], fetch: 'EXTRA_LAZY')]
    protected Collection $voter;
    
    /** Модификатор */
    #[ORM\OneToOne(mappedBy: 'event', targetEntity: RoleModify::class, cascade: ['all'])]
    protected RoleModify $modify;
    
    /** Сортировка */
    #[ORM\Column(type: Types::SMALLINT, length: 3, options: ['default' => 500])]
    protected int $sort = 500;
    
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