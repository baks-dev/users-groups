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

namespace App\Module\Users\Groups\Role\UseCase\Admin\NewEdit;

use App\Module\Users\Groups\Role\Entity;
use App\System\Type\Modify\ModifyActionEnum;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RoleHandler
{
	private EntityManagerInterface $entityManager;
	//private ImageUploadInterface $imageUpload;
	private ValidatorInterface $validator;
	private LoggerInterface $logger;
	
	public function __construct(
		EntityManagerInterface $entityManager,
		ValidatorInterface $validator,
		LoggerInterface $logger
		//ImageUploadInterface $imageUpload,
	
	)
	{
		$this->entityManager = $entityManager;
		//$this->imageUpload = $imageUpload;
		$this->validator = $validator;
		$this->logger = $logger;
	}
	
	public function handle(
		\App\Module\Users\Groups\Role\Entity\Event\RoleEventInterface $command,
	) : string|\App\Module\Users\Groups\Role\Entity\Role
	{
		
		/* Валидация */
		$errors = $this->validator->validate($command);
		
		if(count($errors) > 0)
		{
			$uniqid = uniqid('', false);
			$errorsString = (string) $errors;
			$this->logger->error($uniqid.': '.$errorsString);
			return $uniqid;
		}
		
		
		if($command->getEvent())
		{
			$EventRepo = $this->entityManager->getRepository(\App\Module\Users\Groups\Role\Entity\Event\RoleEvent::class)->find($command->getEvent());
			
			if($EventRepo === null)
			{
				$uniqid = uniqid('', false);
				$errorsString = sprintf(
					'Ошибка при получении сущности %s с id: %s',
					\App\Module\Users\Groups\Role\Entity\Event\RoleEvent::class,
					$command->getEvent()
				);
				$this->logger->error($uniqid.': '.$errorsString);
				return $uniqid;
			}
			
			$Event = $EventRepo->cloneEntity();
		} else
		{
			$Event = new \App\Module\Users\Groups\Role\Entity\Event\RoleEvent();
		}
		
		$Event->setEntity($command);
		$this->entityManager->clear();
		$this->entityManager->persist($Event);
		
		
		if(empty($Event->getRole()))
		{
			$uniqid = uniqid('', false);
			$errorsString = sprintf('Необходимо указать роль группы');
			$this->logger->error($uniqid.': '.$errorsString);
			return $uniqid;
		}
		
		
		$this->entityManager->persist($Event);
		
		$Role = $this->entityManager->getRepository(\App\Module\Users\Groups\Role\Entity\Role::class)->find($Event->getRole());
		
		if(empty($Role))
		{
			$Role = new \App\Module\Users\Groups\Role\Entity\Role($Event->getRole());
			$this->entityManager->persist($Role);
		}
		
		//			/* Восстанавливаем из корзины */
		//			if($Event->isModifyActionEquals(ModifyActionEnum::RESTORE))
		//			{
		//				$remove = $this->entityManager->getRepository(Entity\Event\RoleEvent::class)
		//					->find($command->getEvent())
		//				;
		//				$this->entityManager->remove($remove);
		//			}
		
		$Event->setRole($Role);
		$Role->setEvent($Event);
		
		//			/* Удаляем категорию */
		//			if($Event->isModifyActionEquals(ModifyActionEnum::DELETE))
		//			{
		//				$this->entityManager->remove($Role);
		//			}
		
		$this->entityManager->flush();
		
		return $Role;
		
		
	}
	
}