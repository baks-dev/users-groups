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

namespace BaksDev\Users\Groups\Users\Repository\RoleByUser;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Users\Groups\Group\Entity as EntityGroup;
use BaksDev\Users\Groups\Users\Entity;
use BaksDev\Users\User\Type\Id\UserUid;

final class RoleByUserRepository implements RoleByUserInterface
{
    private ORMQueryBuilder $ORMQueryBuilder;
    private DBALQueryBuilder $DBALQueryBuilder;

    public function __construct(DBALQueryBuilder $DBALQueryBuilder, ORMQueryBuilder $ORMQueryBuilder)
    {
        $this->ORMQueryBuilder = $ORMQueryBuilder;
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }

    public function get(UserUid $userUid): array
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $qb->select(['groups_event', 'check_role', 'check_voter']);

        $qb->from(Entity\CheckUsers::class, 'check');

        $qb->join(
            Entity\Event\CheckUsersEvent::class,
            'check_event',
            'WITH',
            'check_event.id = check.event'
        );

        $qb->join(EntityGroup\Group::class, 'groups', 'WITH', 'groups.id = check_event.group');
        $qb->join(
            EntityGroup\Event\GroupEvent::class,
            'groups_event',
            'WITH',
            'groups_event.id = groups.event'
        );

        $qb->leftJoin(
            EntityGroup\CheckRole\CheckRole::class,
            'check_role',
            'WITH',
            'check_role.event = groups.event'
        );

        $qb->leftJoin(
            EntityGroup\CheckRole\CheckVoter\CheckVoter::class,
            'check_voter',
            'WITH',
            'check_voter.check = check_role.id'
        );

        $qb->where('check.id = :user_id');
        $qb->setParameter('user_id', $userUid, UserUid::TYPE);

        /* Кешируем результат ORM */
        return $qb->enableCache('UserGroup', 86400)->getResult();
    }

    public function fetchAllRoleUser(UserUid $userUid)
    {
        $qb = $this->DBALQueryBuilder->createQueryBuilder(self::class);

       $qb->select('
            DISTINCT UNNEST(
                ARRAY_AGG(groups.id) || 
                ARRAY_AGG(check_role.role) || 
                ARRAY_AGG(check_voter.voter)
            ) AS roles
        ');

        $qb->from(Entity\CheckUsers::TABLE, 'check_user');

        $qb->join(
            'check_user',
            Entity\Event\CheckUsersEvent::TABLE,
            'check_event',
            'check_event.id = check_user.event'
        );

        $qb->join(
            'check_event',
            EntityGroup\Group::TABLE,
            'groups',
            'groups.id = check_event.group_id'
        );

        $qb->join(
            'groups',
            EntityGroup\Event\GroupEvent::TABLE,
            'groups_event',
            'groups_event.id = groups.event'
        );

        $qb->leftJoin(
            'groups',
            EntityGroup\CheckRole\CheckRole::TABLE,
            'check_role',
            'check_role.event = groups.event'
        );

        $qb->leftJoin(
            'check_role',
            EntityGroup\CheckRole\CheckVoter\CheckVoter::TABLE,
            'check_voter',
            'check_voter.check_id = check_role.id'
        );

        $qb->where('check_user.user_id = :users');
        $qb->setParameter('users', $userUid, UserUid::TYPE);

        /* Кешируем результат DBAL */
        return $qb
            ->enableCache('UserGroup', 3600)
            ->fetchAllAssociative();

    }
}
