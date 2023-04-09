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

namespace BaksDev\Users\Groups\Group\Repository\ChoiceGroups;

use BaksDev\Users\Groups\Group\Entity as EntityGroup;
use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefix;
use BaksDev\Users\Groups\Group\Repository\ChoiceGroups\ChoiceGroupsInterface;
use BaksDev\Core\Type\Locale\Locale;

//use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

//use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ChoiceGroupsRepository implements ChoiceGroupsInterface
{
	
	private EntityManagerInterface $entityManager;
	
	private TranslatorInterface $translator;
	
	
	public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
	{
		$this->entityManager = $entityManager;
		$this->translator = $translator;
	}
	
	
	public function get() : mixed
	{
		$qb = $this->entityManager->createQueryBuilder();
		
		$select = sprintf('new %s(groups.id, trans.name)', GroupPrefix::class);
		
		$qb->select($select);
		$qb->from(EntityGroup\Group::class, 'groups');
		$qb->join(EntityGroup\Event\GroupEvent::class,
			'event',
			'WITH',
			'event.id = groups.event'
		);
		$qb->join(EntityGroup\Trans\GroupTrans::class,
			'trans',
			'WITH',
			'trans.event = groups.event AND trans.local = :local'
		);
		
		$qb->setParameter('local', new Locale($this->translator->getLocale()), Locale::TYPE);
		
		return $qb->getQuery()->getResult();
	}
	
}