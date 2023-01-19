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

namespace App\Module\Users\Groups\Role\Repository\TruncateRole;

use App\Module\Users\Groups\Role\Entity\Event\RoleEvent;
use App\Module\Users\Groups\Role\Entity\Modify\RoleModify;
use App\Module\Users\Groups\Role\Entity\Role;
use App\Module\Users\Groups\Role\Entity\Trans\RoleTrans;
use App\Module\Users\Groups\Role\Entity\Voters\RoleVoter;
use App\Module\Users\Groups\Role\Entity\Voters\Trans\VoterTrans;
use App\Module\Users\Groups\Role\Repository\TruncateRole\TruncateRoleInterface;
use Doctrine\DBAL\Connection;

final class TruncateRole implements TruncateRoleInterface
{
    
    private Connection $connection;
    
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    public function clear() : void
    {
        $qb = $this->connection;
        
        $qb->prepare("TRUNCATE TABLE ".VoterTrans::TABLE." CASCADE")->executeQuery();
        $qb->prepare("TRUNCATE TABLE ".RoleVoter::TABLE." CASCADE")->executeQuery();
        
        $qb->prepare("TRUNCATE TABLE ".RoleModify::TABLE." CASCADE")->executeQuery();
        $qb->prepare("TRUNCATE TABLE ".RoleTrans::TABLE." CASCADE")->executeQuery();
        $qb->prepare("TRUNCATE TABLE ".RoleEvent::TABLE." CASCADE")->executeQuery();
        $qb->prepare("TRUNCATE TABLE ".Role::TABLE." CASCADE")->executeQuery();
        
    }
    
}