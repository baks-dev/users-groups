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

namespace BaksDev\Users\Groups\Role\Entity\Voters;

use BaksDev\Users\Groups\Role\Entity\Event\RoleEvent;
use BaksDev\Users\Groups\Role\Type\Voter\RoleVoterUid;
use BaksDev\Users\Groups\Role\Entity\Voters\RoleVoterInterface;
use BaksDev\Users\Groups\Role\Type\VoterPrefix\VoterPrefix;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Voter */

#[ORM\Entity]
#[ORM\Table(name: 'users_voter')]
#[ORM\Index(columns: ['event'])]
class RoleVoter extends EntityEvent
{
    public const TABLE = 'users_voter';
    
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: RoleVoterUid::TYPE)]
    protected RoleVoterUid $id;
    
    /** Связь на событие */
    #[ORM\ManyToOne(targetEntity: RoleEvent::class, inversedBy: "voter")]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: "id")]
    protected RoleEvent $event;

    /** Префикс правила */
    #[ORM\Column(type: VoterPrefix::TYPE)]
    protected VoterPrefix $voter;
    
    /** Настройки локали */
    #[ORM\OneToMany(mappedBy: 'voter', targetEntity: \BaksDev\Users\Groups\Role\Entity\Voters\Trans\VoterTrans::class, cascade: ['all'])]
    protected Collection $translate;
    
    /**
     * @param RoleEvent $event
     */
    public function __construct(RoleEvent $event) {
        $this->event = $event;
        $this->id = new RoleVoterUid();
    }
   
    public function __clone() : void
    {
        $this->id = new RoleVoterUid();
    }
    
    /**
     * @return RoleVoterUid
     */
    public function getId() : RoleVoterUid
    {
        return $this->id;
    }
    
    

    
//    /** Отмеченные правила роли группы */
//    #[ORM\OneToMany(mappedBy: 'voter', targetEntity: CheckVoter::class, cascade: ['all'])]
//    protected Collection $checkVoter;
    

    
    //    /**
    //     * @param RoleVoter|VoterPrefix $voter
    //     * @param GroupRole|RolePrefix $role
    //     */
    //    public function __construct(RoleVoter|VoterPrefix $voter, GroupRole|RolePrefix $role)
    //    {
    //        $this->role = $role instanceof GroupRole ? $role->getId() : $role;
    //        $this->id = $voter instanceof RoleVoter ? $voter->getId() : $voter;
    //    }
    //
    //
    //    /**
    //     * @return VoterPrefix
    //     */
    //    public function getId() : VoterPrefix
    //    {
    //        return $this->id;
    //    }

    
    /**
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof RoleVoterInterface)
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
        if($dto instanceof RoleVoterInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    public function getNameByLocale(Locale $locale) : ?string
    {
        $name = null;
        
        /** @var \BaksDev\Users\Groups\Role\Entity\Voters\Trans\VoterTrans $trans */
        foreach($this->translate as $trans)
        {
            if($name = $trans->name($locale))
            {
                break;
            }
        }
        
        return $name;
    }
    
}