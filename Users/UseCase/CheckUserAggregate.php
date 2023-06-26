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

namespace BaksDev\Users\Groups\Users\UseCase;

use BaksDev\Core\Services\Messenger\MessageDispatchInterface;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use BaksDev\Users\Groups\Users\Entity;
use BaksDev\Users\Groups\Users\Messenger\GroupCheckUserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CheckUserAggregate
{
    private EntityManagerInterface $entityManager;

    private ValidatorInterface $validator;

    private MessageDispatchInterface $messageDispatch;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        MessageDispatchInterface $messageDispatch
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->messageDispatch = $messageDispatch;
    }

    public function handle(
        Entity\Event\CheckUsersEventInterface $command,
    ): mixed {
        // ВАЛИДАЦИЯ
        $errors = $this->validator->validate($command);

        if (count($errors) > 0)
        {
            $errorsString = (string) $errors;

            throw new ValidatorException($errorsString);
        }

        // HANDLE

        if ($command->getEvent())
        {
            $EventRepo = $this->entityManager->getRepository(
                Entity\Event\CheckUsersEvent::class
            )
                ->find($command->getEvent());
            $Event = $EventRepo->cloneEntity();
        } else
        {
            $Event = new Entity\Event\CheckUsersEvent();
        }

        $Event->setEntity($command);
        $this->entityManager->clear();
        $this->entityManager->persist($Event);

        if ($Event->getUser())
        {
            $CheckUsers = $this->entityManager->getRepository(Entity\CheckUsers::class)
                ->find($Event->getUser());

            if (empty($CheckUsers))
            {
                $CheckUsers = new Entity\CheckUsers($Event->getUser());
                $this->entityManager->persist($CheckUsers);
            }

            // Восстанавливаем из корзины
            if ($Event->isModifyActionEquals(ModifyActionEnum::RESTORE))
            {
                $remove = $this->entityManager->getRepository(
                    Entity\Event\CheckUsersEvent::class
                )
                    ->find($command->getEvent());
                $this->entityManager->remove($remove);
            }

            $Event->setUser($CheckUsers);
            $CheckUsers->setEvent($Event);

            // Удаляем пользователя из группы
            if ($Event->isModifyActionEquals(ModifyActionEnum::DELETE))
            {
                $this->entityManager->remove($CheckUsers);
            }

            $this->entityManager->flush();

            //dd($CheckUsers);

            //			/* Сбрасываем кеш группы пользователя */
            //			$cache = new FilesystemAdapter();
            //			$cache->delete('group-'.$Event->getUser()->getValue());




            /* Отправляем событие в шину  */
            $this->messageDispatch->dispatch(
                message: new GroupCheckUserMessage(
                    $CheckUsers->getId(),
                    $CheckUsers->getEvent(),
                    $command->getEvent()
                ),
                transport: 'users_groups'
            );


            return $CheckUsers;
        }

        return false;
    }
}
