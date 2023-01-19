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

namespace BaksDev\Users\Groups\Group\Entity\CheckRole\CheckVoter;

use BaksDev\Users\Groups\Group\Entity\CheckRole\CheckRole;
use BaksDev\Users\Groups\Group\Entity\Event\GroupEvent;

use BaksDev\Users\Groups\Group\Entity\CheckRole\CheckVoter\CheckVoterInterface;
use BaksDev\Users\Groups\Role\Type\VoterPrefix\VoterPrefix;
use BaksDev\Core\Entity\EntityEvent;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/** Отмеченные правила для роли группы */

#[ORM\Entity]
#[ORM\Table(name: 'users_group_check_voter')]
class CheckVoter extends EntityEvent
{
    public const TABLE = 'users_group_check_voter';
    
    /** Связь на событие группы */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: CheckRole::class, inversedBy: "voter")]
    #[ORM\JoinColumn(name: 'check_id', referencedColumnName: "id")]
    protected CheckRole $check;

    /** Префикс правила */
    #[ORM\Id]
    #[ORM\Column(type: VoterPrefix::TYPE)]
    protected VoterPrefix $voter;
    

    public function __construct(CheckRole $check) { $this->check = $check; }
    
    
    /**
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof CheckVoterInterface)
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
        if($dto instanceof CheckVoterInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    public function getUserRole() : VoterPrefix
    {
        return $this->voter;
    }
    
//    public function equals($dto) : bool
//    {
//        if($dto instanceof CheckVoterInterface)
//        {
//            return  ($this->check->getId()->getValue() === $dto->getEquals()?->getValue() &&
//              $this->voter->getValue() === $dto->getVoter()->getValue());
//        }
//
//        throw new Exception(sprintf('Class %s interface error', $dto::class));
//    }

    
}
