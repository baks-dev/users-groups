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

namespace BaksDev\Users\Groups\Users\Repository\UsersGroupChoiceForm;

use BaksDev\Auth\Email\Entity;
use BaksDev\Auth\Email\Type\Status\AccountStatus;
use BaksDev\Auth\Email\Type\Status\AccountStatusEnum;
use BaksDev\Users\Groups\Users\Entity\CheckUsers;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;
use Doctrine\ORM\EntityManagerInterface;

final class UsersGroupChoiceRepository implements UsersGroupChoiceInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(): mixed
    {
        $qb = $this->entityManager->createQueryBuilder();

        $select = sprintf('new %s(users.id, account_event.email)', UserUid::class);

        $qb->select($select);
        // $qb->addSelect('users.id');

        $qb->from(User::class, 'users', 'users.id');

        $qb->join(
            Entity\Account::class,
            'account',
            'WITH',
            'account.id = users.id'
        );

        $qb->join(
            Entity\Event\AccountEvent::class,
            'account_event',
            'WITH',
            'account_event.id = account.event'
        );

        $qb->join(
            Entity\Status\AccountStatus::class,
            'account_status',
            'WITH',
            'account_status.event = account_event.id AND account_status.status = :status'
        );

        // Только активные пользователи
        $status = new AccountStatus(AccountStatusEnum::ACTIVE);
        $qb->setParameter('status', $status, AccountStatus::TYPE);

        // NOT EXIST
        $qbExistGroup = $this->entityManager->createQueryBuilder();
        $qbExistGroup->select('1')
            ->from(CheckUsers::class, 'checker')
            ->where('checker.id = users.id')
        ;

        $qb->andWhere($qb->expr()->not($qb->expr()->exists($qbExistGroup->getDQL())));


        return $qb->getQuery()->getResult();
    }
}
