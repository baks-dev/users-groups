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
use BaksDev\Users\Groups\Group\Entity\Event\GroupEventInterface;
use BaksDev\Users\Groups\Users\Entity\CheckUsers;
use BaksDev\Users\Groups\Users\Entity\Event\CheckUsersEvent;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class GroupHandler
{
	private EntityManagerInterface $entityManager;
	private RequestStack $request;
	private TranslatorInterface $translator;
	private ValidatorInterface $validator;
	private LoggerInterface $logger;
	
	public function __construct(
		EntityManagerInterface $entityManager,
		RequestStack $request,
		TranslatorInterface $translator,
		ValidatorInterface $validator,
		LoggerInterface $logger
	)
	{
		$this->entityManager = $entityManager;
		$this->request = $request;
		$this->translator = $translator;
		$this->validator = $validator;
		$this->logger = $logger;
	}
	
	public function handle(
		GroupEventInterface $command
	) : string|\BaksDev\Users\Groups\Group\Entity\Event\GroupEvent
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
			$EventRepo = $this->entityManager->getRepository(\BaksDev\Users\Groups\Group\Entity\Event\GroupEvent::class)
				->find($command->getEvent()
			);
			
			if($EventRepo === null)
			{
				$uniqid = uniqid('', false);
				$errorsString = sprintf(
					'Ошибка при получении сущности %s с id: %s',
					\BaksDev\Users\Groups\Group\Entity\Event\GroupEvent::class,
					$command->getEvent()
				);
				$this->logger->error($uniqid.': '.$errorsString);
				return $uniqid;
			}
			
			$Event = $EventRepo->cloneEntity();
		} else
		{
			$Event = new \BaksDev\Users\Groups\Group\Entity\Event\GroupEvent();
		}
		
		
		
		$this->entityManager->clear();
		
		$Event->setEntity($command);
		$this->entityManager->persist($Event);
		
		
		if(empty($Event->getGroup()))
		{
			$uniqid = uniqid('', false);
			$errorsString = sprintf(
				'%s: Не указан идентификатор (префикс) группы',
				self::class
			);
			$this->logger->error($uniqid.': '.$errorsString);
			return $uniqid;
		}
		
		
		/* Делаем проверку, что префикс свободен */
		if($command->getEvent() === null)
		{
			$GroupExist = $this->entityManager->getRepository(\BaksDev\Users\Groups\Group\Entity\Group::class)->find($Event->getGroup());
			
			if(!empty($GroupExist))
			{
				$uniqid = uniqid('', false);
				$errorsString = sprintf(
					'%s: Группа с префиксом %s уже добавлена',
					self::class,
					$Event->getGroup()
				);
				$this->logger->error($uniqid.': '.$errorsString);
			
				/* Уведомление пользовтаелю */
				$this->request->getSession()->getFlashBag()->add(
					'danger',
					$this->translator->trans('Группа с таким префиксом уже существует')
				);
				
				return $uniqid;
			}
		}
		
		$Group = $this->entityManager->getRepository(\BaksDev\Users\Groups\Group\Entity\Group::class)->findOneBy(
			['event' => $command->getEvent()]
		);
		
		if(empty($Group))
		{
			$Group = new \BaksDev\Users\Groups\Group\Entity\Group($Event->getGroup());
			$this->entityManager->persist($Group);
		}
		
		$Group->setEvent($Event); /* Обновляем событие */
		$this->entityManager->flush();
		
		return $Event;
		
	}
}