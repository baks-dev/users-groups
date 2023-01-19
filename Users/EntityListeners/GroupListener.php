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

namespace App\Module\Users\Groups\Users\EntityListeners;

use App\Module\Users\Groups\Group\Entity\Group;
use App\Module\Users\Groups\Group\Entity\Modify\GroupModify;
use App\Module\Users\Groups\Users\Repository\UsersByGroup\UsersByGroupInterface;
use App\System\Type\Ip\IpAddress;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class GroupListener
{
    
    private UsersByGroupInterface $usersByGroup;
    
    public function __construct(UsersByGroupInterface $usersByGroup)
    {
        
        $this->usersByGroup = $usersByGroup;
    }
    
    public function preUpdate(Group $data, LifecycleEventArgs $event) : void
    {
        $users = $this->usersByGroup->get($data->getId());
        
        $cache = new FilesystemAdapter();

        foreach($users as $user)
        {
            $cache->delete('group-'.$user['user_id']);
        }
    }
    
}