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

namespace BaksDev\Users\Groups\Group\Repository\AllGroups;

use BaksDev\Users\Groups\Group\Entity;
use BaksDev\Users\Groups\Group\Repository\AllGroups\AllGroupsInterface;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Core\Services\Switcher\Switcher;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

use function BaksDev\Users\Groups\Group\Repository\AllGroups\mb_strtolower;

final class AllGroupsQuery implements AllGroupsInterface
{
	private Connection $connection;
	
	private Switcher $switcher;
	
	private PaginatorInterface $paginator;
	
	private TranslatorInterface $translator;
	
	
	public function __construct(
		Connection $connection,
		TranslatorInterface $translator,
		Switcher $switcher,
		PaginatorInterface $paginator,
	)
	{
		$this->connection = $connection;
		$this->switcher = $switcher;
		$this->paginator = $paginator;
		$this->translator = $translator;
	}
	
	
	public function get(SearchDTO $search) : PaginatorInterface
	{
		$qb = $this->connection->createQueryBuilder();
		
		$qb->select('groups.id');
		$qb->addSelect('groups.event');
		$qb->from(\BaksDev\Users\Groups\Group\Entity\Group::TABLE, 'groups');
		
		$qb->addSelect('event.sort');
		$qb->join('groups',
			\BaksDev\Users\Groups\Group\Entity\Event\GroupEvent::TABLE,
			'event',
			'event.id = groups.event'
		);
		
		$qb->addSelect('trans.name');
		$qb->addSelect('trans.description');
		$qb->join(
			'event',
			\BaksDev\Users\Groups\Group\Entity\Trans\GroupTrans::TABLE,
			'trans',
			'trans.event = event.id AND trans.local = :local'
		);
		$qb->setParameter('local', new Locale($this->translator->getLocale()), Locale::TYPE);
		
		/* Поиск */
		if($search->query)
		{
			$search->query = mb_strtolower($search->query);
			
			$searcher = $this->connection->createQueryBuilder();
			
			$searcher->orWhere('LOWER(groups.id) LIKE :query');
			$searcher->orWhere('LOWER(groups.id) LIKE :switcher');
			
			$searcher->orWhere('LOWER(trans.name) LIKE :query');
			$searcher->orWhere('LOWER(trans.name) LIKE :switcher');
			
			$searcher->orWhere('LOWER(trans.description) LIKE :query');
			$searcher->orWhere('LOWER(trans.description) LIKE :switcher');
			
			$qb->andWhere('('.$searcher->getQueryPart('where').')');
			$qb->setParameter('query', '%'.$this->switcher->toRus($search->query).'%');
			$qb->setParameter('switcher', '%'.$this->switcher->toEng($search->query).'%');
		}
		
		$qb->orderBy('event.sort');
		
		return $this->paginator->fetchAllAssociative($qb);
		
		//return $qb;
	}
	
}