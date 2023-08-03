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

namespace BaksDev\Users\Groups\Group\Repository\AllGroups;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Users\Groups\Group\Entity\Event\GroupEvent;
use BaksDev\Users\Groups\Group\Entity\Group;
use BaksDev\Users\Groups\Group\Entity\Trans\GroupTrans;

final class AllGroupsQuery implements AllGroupsInterface
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


    public function get(SearchDTO $search): PaginatorInterface
    {
        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal()
        ;

        $qb->select('groups.id');
        $qb->addSelect('groups.event');
        $qb->from(Group::TABLE, 'groups');

        $qb->addSelect('event.sort');
        $qb->join('groups',
            GroupEvent::TABLE,
            'event',
            'event.id = groups.event'
        );

        $qb->addSelect('trans.name');
        $qb->addSelect('trans.description');
        $qb->join(
            'event',
            GroupTrans::TABLE,
            'trans',
            'trans.event = event.id AND trans.local = :local'
        );

        /* Поиск */
        if($search->getQuery())
        {
            $qb
                ->createSearchQueryBuilder($search)

                ->addSearchLike('groups.id')
                ->addSearchLike('trans.name')
                ->addSearchLike('trans.description')
            ;
        }

        $qb->orderBy('event.sort');

        return $this->paginator->fetchAllAssociative($qb);

        //return $qb;
    }

}