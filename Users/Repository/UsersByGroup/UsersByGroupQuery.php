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

namespace App\Module\Users\Groups\Users\Repository\UsersByGroup;

use App\Module\Users\Groups\Group\Type\Prefix\GroupPrefix;
use App\Module\Users\Groups\Users\Entity;
use App\Module\Users\Groups\Users\Repository\UsersByGroup\UsersByGroupInterface;
use Doctrine\DBAL\Connection;

final class UsersByGroupQuery implements UsersByGroupInterface
{
    
    private Connection $connection;
    
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    public function get(GroupPrefix $prefix) : array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('check_users.user_id');
        $qb->from(\App\Module\Users\Groups\Users\Entity\CheckUsers::TABLE, 'check_users');
        $qb->join(
          'check_users',
          \App\Module\Users\Groups\Users\Entity\Event\CheckUsersEvent::TABLE,
          'check_event',
          'check_event.id = check_users.event AND check_event.group_id = :prefix');
        $qb->setParameter('prefix', $prefix, GroupPrefix::TYPE);

        return $qb->executeQuery()->fetchAllAssociative();
    }
    
}