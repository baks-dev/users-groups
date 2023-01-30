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

namespace BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit;

use BaksDev\Users\Groups\Group\Entity;
use BaksDev\Users\Groups\Group\Entity\CheckRole\CheckRoleInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CheckRoleHandler
{
	private EntityManagerInterface $entityManager;
	
	//private RequestStack $request;
	//private TranslatorInterface $translator;
	private ValidatorInterface $validator;
	
	private LoggerInterface $logger;
	
	
	public function __construct(
		EntityManagerInterface $entityManager,
		ValidatorInterface $validator,
		LoggerInterface $logger,
	)
	{
		$this->entityManager = $entityManager;
		$this->validator = $validator;
		$this->logger = $logger;
	}
	
	
	public function handle(
		CheckRoleInterface $command,
	) : string|\BaksDev\Users\Groups\Group\Entity\CheckRole\CheckRole
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
		
		if(empty($command->getEvent()) || empty($command->getRole()))
		{
			$uniqid = uniqid('', false);
			$errorsString = sprintf('Не указана роль или событие %s',
				\BaksDev\Users\Groups\Group\Entity\CheckRole\CheckRole::class
			);
			$this->logger->error($uniqid.': '.$errorsString);
			
			return $uniqid;
		}
		
		if($command->getEvent())
		{
			$Event = $this->entityManager->getRepository(\BaksDev\Users\Groups\Group\Entity\CheckRole\CheckRole::class)
				->findOneBy(
					['event' => $command->getEvent(), 'role' => $command->getRole()]
				)
			;
			
			if(empty($Event))
			{
				$Event = new \BaksDev\Users\Groups\Group\Entity\CheckRole\CheckRole($command->getEvent());
				$this->entityManager->persist($Event);
				$Event->setEntity($command);
				$this->entityManager->flush();
			}
			
			return $Event;
		}
		
		return false;
	}
	
}