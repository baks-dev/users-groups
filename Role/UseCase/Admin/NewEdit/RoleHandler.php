<?php
/*
 * Copyright (c) 2022-2023.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Users\Groups\Role\UseCase\Admin\NewEdit;

use BaksDev\Core\Services\Messenger\MessageDispatchInterface;
use BaksDev\Users\Groups\Role\Entity;
use BaksDev\Users\Groups\Role\Messenger\UserRoleMessage;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RoleHandler
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
        Entity\Event\RoleEventInterface $command,
    ): string|Entity\Role {
        if ($command->getEvent())
        {
            $EventRepo = $this->entityManager->getRepository(Entity\Event\RoleEvent::class)
                ->find($command->getEvent());

            if ($EventRepo === null)
            {
                $uniqid = uniqid('', false);
                $errorsString = sprintf(
                    'Ошибка при получении сущности %s с id: %s',
                    Entity\Event\RoleEvent::class,
                    $command->getEvent()
                );
                $this->logger->error($uniqid.': '.$errorsString);

                return $uniqid;
            }

            $Event = $EventRepo->cloneEntity();
        } else
        {
            $Event = new Entity\Event\RoleEvent();
        }

        $Event->setEntity($command);
        $this->entityManager->clear();
        $this->entityManager->persist($Event);

        if (empty($Event->getRole()))
        {
            $uniqid = uniqid('', false);
            $errorsString = sprintf('Необходимо указать роль группы');
            $this->logger->error($uniqid.': '.$errorsString);

            return $uniqid;
        }

        $this->entityManager->persist($Event);

        $Role = $this->entityManager->getRepository(Entity\Role::class)
            ->find($Event->getRole());

        if (empty($Role))
        {
            $Role = new Entity\Role($Event->getRole());
            $this->entityManager->persist($Role);
        }

        $Event->setRole($Role);
        $Role->setEvent($Event);

        /* Валидация события */
        $errors = $this->validator->validate($Event);

        if (count($errors) > 0)
        {
            $uniqid = uniqid('', false);
            $errorsString = (string) $errors;
            $this->logger->error($uniqid.': '.$errorsString);

            return $uniqid;
        }

        /* Валидация Main */
        $errors = $this->validator->validate($Role);

        if (count($errors) > 0)
        {
            $uniqid = uniqid('', false);
            $errorsString = (string) $errors;
            $this->logger->error($uniqid.': '.$errorsString);

            return $uniqid;
        }

        $this->entityManager->flush();



        /* Отправляем событие в шину  */
        $this->messageDispatch->dispatch(
            message: new UserRoleMessage($Role->getId(), $Role->getEvent(), $command->getEvent()),
            transport: 'users_groups'
        );



        return $Role;
    }
}
