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

namespace BaksDev\Users\Groups\Group\Repository\GroupByPrefix;

use BaksDev\Users\Groups\Group\Entity;
use BaksDev\Users\Groups\Group\Repository\GroupByPrefix\GroupByPrefixInterface;
use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefix;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

final class GroupByPrefixRepository implements GroupByPrefixInterface
{
	private EntityManagerInterface $entityManager;
	
	
	public function __construct(EntityManagerInterface $entityManager)
	{
		
		$this->entityManager = $entityManager;
	}
	
	
	/**
	 * @throws NonUniqueResultException
	 */
	public function get(GroupPrefix $prefix) : ?\BaksDev\Users\Groups\Group\Entity\Event\GroupEvent
	{
		$qb = $this->entityManager->createQueryBuilder();
		
		$qb->select('event');
		$qb->from(\BaksDev\Users\Groups\Group\Entity\Group::class, 'groups');
		$qb->join(\BaksDev\Users\Groups\Group\Entity\Event\GroupEvent::class,
			'event',
			'WITH',
			'event.id = groups.event'
		);
		$qb->where('groups.id = :prefix');
		$qb->setParameter('prefix', $prefix, GroupPrefix::TYPE);
		
		return $qb->getQuery()->getOneOrNullResult();
	}
	
}