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

use BaksDev\Users\Groups\Users\Entity;
use BaksDev\Users\Groups\Group\Entity\CheckRole\CheckRoleInterface;
use BaksDev\Users\Groups\Group\Entity\Event\GroupEventInterface;
use BaksDev\Users\Groups\Users\Entity\CheckUserInterface;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CheckUserAggregate
{
    private EntityManagerInterface $entityManager;
    //private RequestStack $request;
    //private TranslatorInterface $translator;
    private ValidatorInterface $validator;
    
    public function __construct(
      EntityManagerInterface $entityManager,
      //RequestStack $request,
      //TranslatorInterface $translator,
      ValidatorInterface $validator
    )
    {
        $this->entityManager = $entityManager;
        //$this->request = $request;
        //$this->translator = $translator;
        $this->validator = $validator;
    }
    
    public function handle(
      \BaksDev\Users\Groups\Users\Entity\Event\CheckUsersEventInterface $command
    ) : mixed
    {

        /* ВАЛИДАЦИЯ */
        $errors = $this->validator->validate($command);
        
        if(count($errors) > 0)
        {
            $errorsString = (string) $errors;
            throw new ValidatorException($errorsString);
        }
        
        
        /* HANDLE */
        
        if($command->getEvent())
        {
            $EventRepo = $this->entityManager->getRepository(\BaksDev\Users\Groups\Users\Entity\Event\CheckUsersEvent::class)
              ->find($command->getEvent());
            $Event = $EventRepo->cloneEntity();
        }
        else
        {
            $Event = new \BaksDev\Users\Groups\Users\Entity\Event\CheckUsersEvent();
        }
        
        $Event->setEntity($command);
        $this->entityManager->clear();
        $this->entityManager->persist($Event);
        
        if($Event->getUser())
        {
            $CheckUsers = $this->entityManager->getRepository(\BaksDev\Users\Groups\Users\Entity\CheckUsers::class)->find($Event->getUser());
            
            if(empty($CheckUsers))
            {
                $CheckUsers = new \BaksDev\Users\Groups\Users\Entity\CheckUsers($Event->getUser());
                $this->entityManager->persist($CheckUsers);
            }
            
            /* Восстанавливаем из корзины */
            if($Event->isModifyActionEquals(ModifyActionEnum::RESTORE))
            {
                $remove = $this->entityManager->getRepository(
					\BaksDev\Users\Groups\Users\Entity\Event\CheckUsersEvent::class)
                  ->find($command->getEvent());
                $this->entityManager->remove($remove);
            }
            
            $Event->setUser($CheckUsers);
            $CheckUsers->setEvent($Event);
            
            /* Удаляем пользователя из группы */
            if($Event->isModifyActionEquals(ModifyActionEnum::DELETE))
            {
                $this->entityManager->remove($CheckUsers);
            }
            
            $this->entityManager->flush();
            
            /* Сбрасываем кеш группы пользователя */
            $cache = new FilesystemAdapter();
            $cache->delete('group-'.$Event->getUser()->getValue());

            return $CheckUsers;
        }
        
        return false;
    }
}