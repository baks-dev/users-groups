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

namespace BaksDev\Users\Groups\Group\UseCase\Admin\Delete;

use BaksDev\Core\Services\Messenger\MessageDispatchInterface;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use BaksDev\Users\Groups\Group\Entity;
use BaksDev\Users\Groups\Group\Entity\Event\GroupEventInterface;
use BaksDev\Users\Groups\Group\Messenger\UserGroupMessage;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DeleteGroupHandler
{
    private EntityManagerInterface $entityManager;

    private ValidatorInterface $validator;

    private LoggerInterface $logger;

    private MessageDispatchInterface $messageDispatch;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        MessageDispatchInterface $messageDispatch
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->messageDispatch = $messageDispatch;
    }

    public function handle(
        GroupEventInterface $command,
    ): string|Entity\Group {
        /* Валидация */
        $errors = $this->validator->validate($command);

        if (count($errors) > 0)
        {
            $uniqid = uniqid('', false);
            $errorsString = (string) $errors;
            $this->logger->error($uniqid.': '.$errorsString);

            return $uniqid;
        }

        $EventRepo = $this->entityManager->getRepository(Entity\Event\GroupEvent::class)
            ->find($command->getEvent());

        if (empty($EventRepo))
        {
            $uniqid = uniqid('', false);
            $errorsString = sprintf(
                '%s: Ошибка при получении сущности GroupEvent с id: %s',
                self::class,
                $command->getEvent()
            );
            $this->logger->error($uniqid.': '.$errorsString);

            return $uniqid;
        }

        /* Клонируем и мапим сущность $Event */
        $Event = $EventRepo->cloneEntity();
        $Event->setEntity($command);

        if (!$Event->isModifyActionEquals(ModifyActionEnum::DELETE))
        {
            $uniqid = uniqid('', false);
            $errorsString = sprintf(
                '%s: Модификатор не соотвтетствует: %s',
                self::class,
                (ModifyActionEnum::DELETE)->name
            );
            $this->logger->error($uniqid.': '.$errorsString);

            return $uniqid;
        }

        $this->entityManager->clear();
        $Group = $this->entityManager->getRepository(
            Entity\Group::class
        )->findOneBy(['event' => $command->getEvent()]);

        if (empty($Group))
        {
            $uniqid = uniqid('', false);
            $errorsString = sprintf(
                '%s: Невозможно получить группу с событием id: %s',
                self::class,
                $command->getEvent()
            );
            $this->logger->error($uniqid.': '.$errorsString);

            return $uniqid;
        }

        /* Удаляем группу */
        $this->entityManager->remove($Group);
        $this->entityManager->persist($Event);
        $this->entityManager->flush();


        /* Отправляем событие в шину  */
        $this->messageDispatch->dispatch(
            message: new UserGroupMessage($Group->getId(), $Group->getEvent(), $command->getEvent()),
            transport: 'users-groups'
        );


        return $Group;
    }
}
