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

namespace BaksDev\Users\Groups\Group\Entity\Trans;

use BaksDev\Users\Groups\Group\Entity\Event\GroupEvent;
use BaksDev\Users\Groups\Group\Entity\Trans\GroupTransInterface;
use BaksDev\Users\Groups\Group\Type\Event\GroupEventUid;
use BaksDev\Users\Groups\Repository\Group\Trans\GroupTransRepository;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* Перевод Group */

#[ORM\Entity]
#[ORM\Table(name: 'users_group_trans')]
class GroupTrans extends EntityEvent
{
    public const TABLE = 'users_group_trans';
    
    /** Связь на событие */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: GroupEvent::class, inversedBy: "translate")]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: "id", nullable: false)]
    protected GroupEvent $event;
    
    /** Локаль */
    #[ORM\Id]
    #[ORM\Column(type: Locale::TYPE, length: 2)]
    protected Locale $local;
    
    /** Название */
    #[ORM\Column(type: Types::STRING, length: 100)]
    protected string $name;
    
    /** Описание */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $description;
    
    /**
     * @param GroupEvent $event
     */
    public function __construct(GroupEvent $event)
    {
        $this->event = $event;
       
    }
    
    public function getDto($dto) : mixed
    {
        if($dto instanceof GroupTransInterface)
        {
            return parent::getDto($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    public function setEntity($dto) : mixed
    {
        if($dto instanceof GroupTransInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    public function name(Locale $locale) : ?string
    {
        if($this->local->getValue() === $locale->getValue())
        {
            return $this->name;
        }
        
        return null;
    }
    
    
//    /**
//     * @param Locale $local
//     * @param string $name
//     * @param string|null $desc
//     * @return void
//     */
//    public function addGroupTrans(Locale $local, string $name, ?string $desc = null) : void
//    {
//        $this->local = $local;
//        $this->name = $name;
//        $this->desc = $desc;
//    }
    
}
