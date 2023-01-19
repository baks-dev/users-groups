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

namespace App\Module\Users\Groups\Role\Entity\Voters\Trans;

use App\Module\Users\Groups\Role\Entity\Voters\RoleVoter;
use App\Module\Users\Groups\Role\Entity\Voters\Trans\VoterTransInterface;
use App\Module\Users\Groups\Role\Type\Voter\RoleVoterUid;
use App\System\Entity\EntityEvent;
use App\System\Type\Locale\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Перевод Voter */

#[ORM\Entity]
#[ORM\Table(name: 'users_voter_trans')]
class VoterTrans  extends EntityEvent
{
    public const TABLE = 'users_voter_trans';
    
    /** Связь на правило */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: RoleVoter::class, inversedBy: "translate")]
    #[ORM\JoinColumn(name: 'voter_id', referencedColumnName: "id")]
    protected RoleVoter $voter;
    
    /** Локаль */
    #[ORM\Id]
    #[ORM\Column(type: Locale::TYPE, length: 2)]
    protected Locale $local;
    
    /** Название */
    #[ORM\Column(type: Types::STRING, length: 100)]
    protected string $name;
    
    public function __construct(RoleVoter $voter) { $this->voter = $voter; }
    
    
    /**
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof VoterTransInterface)
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
        if($dto instanceof VoterTransInterface)
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
    
    public function equals($dto) : bool
    {
        if($dto instanceof VoterTransInterface)
        {
            return  ($this->voter->getId()->getValue() === $dto->getEquals()->getValue() &&
              $dto->getLocal()->getValue() === $this->local->getValue());
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
}
