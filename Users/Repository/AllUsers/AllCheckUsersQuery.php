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

namespace BaksDev\Users\Groups\Users\Repository\AllUsers;

use BaksDev\Auth\Email\Entity as AccountEntity;
use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Users\Groups\Group\Entity as GroupEntity;
use BaksDev\Users\Groups\Users\Entity as GroupCheckUserEntity;

final class AllCheckUsersQuery implements AllCheckUsersInterface
{

    private PaginatorInterface $paginator;
    private DBALQueryBuilder $DBALQueryBuilder;

    public function __construct(
        DBALQueryBuilder $DBALQueryBuilder,
        PaginatorInterface $paginator,
    )
    {
        $this->paginator = $paginator;
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }

    /** Метод возвращает список пользователей, принадлежащих какой либо из групп */
    public function fetchAllUsersOnGroupAssociative(SearchDTO $search): PaginatorInterface
    {
        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal()
        ;

        $qb->addSelect('checker.event as event');
        $qb->from(GroupCheckUserEntity\CheckUsers::TABLE, 'checker');

        $qb->join(
            'checker',
            GroupCheckUserEntity\Event\CheckUsersEvent::TABLE,
            'event',
            'event.id = checker.event'
        );

        // Модификатор
        $qb->addSelect('checker_modify.mod_date as date_update');
        $qb->join(
            'checker',
            GroupCheckUserEntity\Modify\CheckUserModify::TABLE,
            'checker_modify',
            'checker_modify.event = checker.event'
        );

        // АККАУНТ

        $qb->addSelect('account.id AS account_id');
        $qb->join(
            'checker',
            AccountEntity\Account::TABLE,
            'account',
            'account.id = checker.user_id'
        );

        // Событие
        $qb->addSelect('account_event.id as account_event');
        $qb->addSelect('account_event.email AS account_email');
        $qb->join(
            'account',
            AccountEntity\Event\AccountEvent::TABLE,
            'account_event',
            'account_event.id = account.event'
        );

        // Статус
        $qb->addSelect('account_status.status AS account_status');
        $qb->join(
            'account',
            AccountEntity\Status\AccountStatus::TABLE,
            'account_status',
            'account_status.event = account.event'
        );

        // ГРУППА

        $qb->join(
            'event',
            GroupEntity\Group::TABLE,
            'groups',
            'groups.id = event.group_id'
        );

        $qb->addSelect('group_event.sort');
        $qb->join(
            'groups',
            GroupEntity\Event\GroupEvent::TABLE,
            'group_event',
            'group_event.id = groups.event'
        );

        $qb->addSelect('trans.name AS group_name');
        $qb->addSelect('trans.description AS group_desc');
        $qb->join(
            'group_event',
            GroupEntity\Trans\GroupTrans::TABLE,
            'trans',
            'trans.event = group_event.id AND trans.local = :local'
        );

        // Поиск
        if($search->getQuery())
        {
            $qb
                ->createSearchQueryBuilder($search)

                ->addSearchLike('trans.name')
                ->addSearchLike('trans.description')
            ;
        }

        $qb->orderBy('checker_modify.mod_date', 'DESC');


        return $this->paginator->fetchAllAssociative($qb);


    }
}
