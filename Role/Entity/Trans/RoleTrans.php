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

namespace App\Module\Users\Groups\Role\Entity\Trans;

use App\Module\Users\Groups\Role\Entity\Event\RoleEvent;
use App\Module\Users\Groups\Role\Entity\Trans\RoleTransInterface;
use App\System\Entity\EntityEvent;
use App\System\Type\Locale\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Перевод Role */

#[ORM\Entity]
#[ORM\Table(name: 'users_role_trans')]
class RoleTrans extends EntityEvent
{
    public const TABLE = 'users_role_trans';

    /** Связь на событие */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: RoleEvent::class, inversedBy: "translate")]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: "id")]
    protected RoleEvent $event;
    
    /** Локаль */
    #[ORM\Id]
    #[ORM\Column(name: 'local', type: Locale::TYPE, length: 2)]
    protected Locale $local;
    
    /** Название */
    #[ORM\Column(name: 'name', type: Types::STRING, length: 100)]
    protected string $name;
    
    /** Описание */
    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    protected ?string $description;
    

    public function __construct(RoleEvent $event) {
        $this->event = $event;
    }
    
    /**
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof RoleTransInterface)
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
        if($dto instanceof RoleTransInterface)
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
    
//    public function equals($dto) : bool
//    {
//        if($dto instanceof RoleTransInterface)
//        {
//            return  ($this->role->getId()->getValue() === $dto->getEquals()->getValue() &&
//              $dto->getLocal()->getValue() === $this->local->getValue());
//        }
//
//        throw new Exception(sprintf('Class %s interface error', $dto::class));
//    }
}
