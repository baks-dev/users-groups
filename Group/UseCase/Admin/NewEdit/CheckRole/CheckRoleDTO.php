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

namespace App\Module\Users\Groups\Group\UseCase\Admin\NewEdit\CheckRole;

use App\Module\Users\Groups\Group\Entity\CheckRole\CheckRoleInterface;
use App\Module\Users\Groups\Role\Type\RolePrefix\RolePrefix;
use App\Module\Users\Groups\Role\Type\VoterPrefix\VoterPrefix;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class CheckRoleDTO implements CheckRoleInterface
{
    
    private bool $checked = false;
    
    /** Префикс роли */
    #[Assert\NotBlank]
    private RolePrefix $role;
    
    /** Правила роли */
    #[Assert\Valid]
    protected ArrayCollection $voter;
    
    public function __construct()
    {
        $this->voter = new ArrayCollection();
    }
    
    /**
     * @return RolePrefix
     */
    public function getRole() : RolePrefix
    {
        return $this->role;
    }
    
    /**
     * @param RolePrefix $role
     */
    public function setRole(RolePrefix $role) : void
    {
        $this->role = $role;
    }
    
    /* VOTER */
    

    public function getVoter() : array
    {
        return $this->voter->toArray();
    }
    
    /**
     * @param ArrayCollection $voter
     */
    public function addVoter(\App\Module\Users\Groups\Group\UseCase\Admin\NewEdit\CheckRole\CheckVoter\CheckVoterDTO $voter) : void
    {
        if($voter instanceof VoterPrefix)
        {
            $CheckVoterDTO = new \App\Module\Users\Groups\Group\UseCase\Admin\NewEdit\CheckRole\CheckVoter\CheckVoterDTO();
            $CheckVoterDTO->setVoter($voter);
            
            if(!$this->voter->contains($CheckVoterDTO))
            {
                $this->voter->add($CheckVoterDTO);
            }
            
            
        }
        else
        {
            $this->voter->add($voter);
        }

    }
    
    public function removeVoter($voter) : void
    {
        $this->voter->removeElement($voter);
    }
    

    
    /**
     * @return bool
     */
    public function isChecked() : bool
    {
        return $this->checked;
    }
    
    /**
     * @param bool $checked
     */
    public function setChecked(bool $checked) : void
    {
        $this->checked = $checked;
    }
    

    public function checked() : void
    {
        $this->checked = true;
    }
}
