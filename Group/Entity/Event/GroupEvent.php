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

namespace BaksDev\Users\Groups\Group\Entity\Event;

//use BaksDev\Users\Groups\Entity\GroupRole;
use BaksDev\Users\Groups\Group\Entity\CheckRole\CheckRole;
use BaksDev\Users\Groups\Group\Entity\Event\GroupEventInterface;
use BaksDev\Users\Groups\Group\Entity\Modify\GroupModify;
use BaksDev\Users\Groups\Group\Entity\Quota\GroupQuota;
use BaksDev\Users\Groups\Group\Entity\Trans\GroupTrans;
use BaksDev\Users\Groups\Group\Type\Event\GroupEventUid;
use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefix;
//use BaksDev\Users\Groups\Repository\Group\Event\GroupEventRepository;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* События Group */

#[ORM\Entity]
#[ORM\Table(name: 'users_group_event')]
#[ORM\Index(columns: ['group_id'])]
class GroupEvent extends EntityEvent
{
    public const TABLE = 'users_group_event';
    
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: GroupEventUid::TYPE)]
    protected GroupEventUid $id;
    
    #[ORM\Column(name: 'group_id', type: GroupPrefix::TYPE)]
    protected GroupPrefix $group;
    
    /** Сортировка */
    #[ORM\Column(type: Types::SMALLINT, length: 3, options: ['default' => 500])]
    protected int $sort = 500;
    
    /** Модификатор */
    #[ORM\OneToOne(mappedBy: 'event', targetEntity: GroupModify::class, cascade: ['all'])]
    protected GroupModify $modify;
    
    /** Настройки локали */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: GroupTrans::class, cascade: ['all'])]
    protected Collection $translate;
    
    /** Ограничения группы */
    #[ORM\OneToOne(mappedBy: 'event', targetEntity: GroupQuota::class, cascade: ['all'])]
    protected GroupQuota $quota;
    
    /** Роли группы */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: CheckRole::class, cascade: ['all'])]
    protected Collection $role;
    
    public function __construct() {
        $this->id = new GroupEventUid();
        $this->modify = new GroupModify($this, new ModifyAction(ModifyActionEnum::NEW));
        $this->quota = new GroupQuota($this);
    }
    
    public function __clone()
    {
        $this->id = new GroupEventUid();
    }

    public function getId() : GroupEventUid
    {
        return $this->id;
    }

    /**
     * @return GroupPrefix
     */
    public function getGroup() : GroupPrefix
    {
        return $this->group;
    }
    
//    /**
//     * @param GroupPrefix $group
//     */
//    public function setGroup(GroupPrefix $group) : void
//    {
//        $this->group = $group;
//    }
    
    
    /**
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof GroupEventInterface)
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
        if($dto instanceof GroupEventInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    public function isModifyActionEquals(ModifyActionEnum $action) : bool
    {
        return $this->modify->equals($action);
    }
    
    
    public function getNameByLocale(Locale $locale) : ?string
    {
        $name = null;
        
        /** @var GroupTrans $trans */
        foreach($this->translate as $trans)
        {
            if($name = $trans->name($locale))
            {
                break;
            }
        }
        
        return $name;
    }
    
    
    public function getUserRole() : GroupPrefix
    {
        return $this->group;
    }
}