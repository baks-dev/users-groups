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

namespace BaksDev\Users\Groups\Users\Repository\RoleByUser;

use BaksDev\Users\Groups\Group\Entity as EntityGroup;
use BaksDev\Users\Groups\Users\Entity;
use BaksDev\Users\User\Type\Id\UserUid;
use BaksDev\Users\Groups\Users\Repository\RoleByUser\RoleByUserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\ApcuAdapter;

final class RoleByUserRepository implements RoleByUserInterface
{
	
	private EntityManagerInterface $entityManager;
	
	
	public function __construct(EntityManagerInterface $entityManager)
	{
		
		$this->entityManager = $entityManager;
	}
	
	
	public function get(UserUid $userUid) : array
	{
		$qb = $this->entityManager->createQueryBuilder();
		
		$qb->select(['groups_event', 'check_role', 'check_voter']);
		
		$qb->from(Entity\CheckUsers::class, 'check');
		$qb->join(Entity\Event\CheckUsersEvent::class,
			'check_event',
			'WITH',
			'check_event.id = check.event'
		);
		
		$qb->join(EntityGroup\Group::class, 'groups', 'WITH', 'groups.id = check_event.group');
		$qb->join(EntityGroup\Event\GroupEvent::class,
			'groups_event',
			'WITH',
			'groups_event.id = groups.event'
		);
		
		$qb->leftJoin(EntityGroup\CheckRole\CheckRole::class,
			'check_role',
			'WITH',
			'check_role.event = groups.event'
		);
		$qb->leftJoin(EntityGroup\CheckRole\CheckVoter\CheckVoter::class,
			'check_voter',
			'WITH',
			'check_voter.check = check_role.id'
		);
		
		$qb->where('check.id = :user_id');
		
		
		/* Кешируем результат ORM */
		$cacheQueries = new ApcuAdapter((string) $userUid->getValue());
		
		$query = $this->entityManager->createQuery($qb->getDQL());
		$query->setQueryCache($cacheQueries);
		$query->setResultCache($cacheQueries);
		$query->enableResultCache();
		$query->setLifetime(60 * 60 * 24);
		
		
		$query->setParameter('user_id', $userUid, UserUid::TYPE);
		
		
		return $query->getResult();

	}
	
}