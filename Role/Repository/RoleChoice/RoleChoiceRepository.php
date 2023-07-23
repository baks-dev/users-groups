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

namespace BaksDev\Users\Groups\Role\Repository\RoleChoice;


use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Users\Groups\Role\Entity\Event\RoleEvent;
use BaksDev\Users\Groups\Role\Entity\Role;
use BaksDev\Users\Groups\Role\Entity\Trans\RoleTrans;
use BaksDev\Users\Groups\Role\Type\RolePrefix\RolePrefix;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RoleChoiceRepository implements RoleChoiceInterface
{
	
	private EntityManagerInterface $entityManager;
	
	private TranslatorInterface $translator;
	
	
	public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
	{
		
		$this->entityManager = $entityManager;
		
		$this->translator = $translator;
	}
	
	
	public function get()
	{
		$qb = $this->entityManager->createQueryBuilder();
		
		$select = sprintf('new %s(event.role, trans.name, trans.description)', RolePrefix::class);
		
		$qb->select($select);
		$qb->from(Role::class, 'role');
		$qb->join(RoleEvent::class, 'event', 'WITH', 'event.id = role.event');
		$qb->join(RoleTrans::class,
			'trans',
			'WITH',
			'trans.event = event.id AND trans.local = :local'
		);
		$qb->setParameter('local', new Locale($this->translator->getLocale()), Locale::TYPE);
		
		return $qb->getQuery()->getResult();
	}
	
}