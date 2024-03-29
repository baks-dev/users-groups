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

namespace BaksDev\Users\Groups\Group\DataFixtures\Security\Group;

use BaksDev\Auth\Email\DataFixtures\Account\AccountFixtures;
use BaksDev\Auth\Email\Entity\Event\AccountEvent;
use BaksDev\Core\Services\Messenger\MessageDispatchInterface;
use BaksDev\Users\Groups\Group\DataFixtures\Security\Group\CheckUser\CheckUsersDTO;
use BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group\GroupDTO;
use BaksDev\Users\Groups\Group\Messenger\UserGroupMessage;
use BaksDev\Users\Groups\Group\Repository\GroupByPrefix\GroupByPrefixInterface;
use BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\GroupHandler;
use BaksDev\Users\Groups\Role\Repository\TruncateRole\TruncateRoleInterface;
use BaksDev\Users\Groups\Users\Entity\CheckUsers;
use BaksDev\Users\Groups\Users\UseCase\CheckUserAggregate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/** Создаем группу Администратор */
final class GroupFixtures extends Fixture implements DependentFixtureInterface
{
    private GroupHandler $aggregate;

    private GroupByPrefixInterface $groupByPrefix;

    private CheckUserAggregate $checkUserAggregate;

    private TruncateRoleInterface $truncateRole;

    private MessageDispatchInterface $messageDispatch;

    public function __construct(
        GroupByPrefixInterface $groupByPrefix,
        GroupHandler $aggregate,
        CheckUserAggregate $checkUserAggregate,
        TruncateRoleInterface $truncateRole,
        MessageDispatchInterface $messageDispatch
    ) {
        $this->aggregate = $aggregate;
        $this->groupByPrefix = $groupByPrefix;
        $this->checkUserAggregate = $checkUserAggregate;
        $this->truncateRole = $truncateRole;
        $this->messageDispatch = $messageDispatch;
    }

    public function load(ObjectManager $manager): void
    {
        // php bin/console doctrine:fixtures:load --append

        // Группа Администраторов
        $GroupDTO = new GroupDTO();

        $GroupEvent = $this->groupByPrefix->get($GroupDTO->getGroup());

        if ($GroupEvent === null)
        {
            $GroupEvent = $this->aggregate->handle($GroupDTO);
        } else
        {
            // Чистим кеш ролей пользователя

            /* Отправляем сообщение в шину */
            $this->messageDispatch->dispatch(
                message: new UserGroupMessage($GroupEvent->getGroup(), $GroupEvent->getId()),
                transport: 'users-groups'
            );

        }

        $this->addReference(self::class, $GroupEvent);

        // сбрасываем роли и правила
        $this->truncateRole->clear();

        // Присваиваем группу администратору
        /** @var AccountEvent $AccountEvent */
        $AccountEvent = $this->getReference(AccountFixtures::class);

        $CheckUsersDTO = new CheckUsersDTO(
            $AccountEvent->getAccount(),
            $GroupEvent->getGroup()
        );

        $CheckUsers = $manager->getRepository(CheckUsers::class)->find($CheckUsersDTO->getUser());

        if (empty($CheckUsers))
        {
            $this->checkUserAggregate->handle($CheckUsersDTO);
        }
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
