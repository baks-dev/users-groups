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

namespace App\Module\Users\Groups\Users\Repository\AllUsers;

use App\Module\Users\Groups\Group\Entity as EntityGroup;
use App\Module\Users\Groups\Users\Entity;
use App\Module\Users\Groups\Users\Repository\AllUsers\EntityAccount;
use App\Module\Users\Groups\Users\Repository\AllUsers\AllCheckUsersInterface;
use App\System\Form\Search\SearchDTO;
use App\System\Services\Paginator\PaginatorInterface;
use App\System\Services\Switcher\Switcher;
use App\System\Type\Locale\Locale;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use function App\Module\Users\Groups\Users\Repository\AllUsers\mb_strtolower;


final class AllCheckUsersQuery implements AllCheckUsersInterface
{
	
	private Connection $connection;
	private Switcher $switcher;
	private PaginatorInterface $paginator;
	
	public function __construct(
		Connection $connection,
		Switcher $switcher,
		PaginatorInterface $paginator
	)
	{
		$this->connection = $connection;
		$this->switcher = $switcher;
		$this->paginator = $paginator;
	}
	
	public function get(SearchDTO $search) : PaginatorInterface
	{
		$qb = $this->connection->createQueryBuilder();
		
		//$qb->select('checker.user_id as id');
		$qb->addSelect('checker.event');
		$qb->from(\App\Module\Users\Groups\Users\Entity\CheckUsers::TABLE, 'checker');
		
		
		$qb->join('checker', \App\Module\Users\Groups\Users\Entity\Event\CheckUsersEvent::TABLE, 'event', 'event.id = checker.event');
		
		/* Модификатор */
		$qb->addSelect('checker_modify.mod_date as update');
		$qb->join(
			'checker',
			\App\Module\Users\Groups\Users\Entity\Modify\CheckUserModify::TABLE,
			'checker_modify',
			'checker_modify.event = checker.event'
		);
		
		
		/* АККАУНТ */
		
		$qb->addSelect('account.id');
		$qb->join('checker', EntityAccount\Account::TABLE, 'account', 'account.id = checker.user_id');
		
		/* Событие */
		$qb->addSelect('account_event.id as account_event');
		$qb->addSelect('account_event.email');
		$qb->join('account', EntityAccount\Event\Event::TABLE, 'account_event', 'account_event.id = account.event');
		
		/* Статус */
		$qb->addSelect('account_status.status');
		$qb->join(
			'account',
			EntityAccount\Status\Status::TABLE,
			'account_status',
			'account_status.event = account.event'
		);
		
		
		/* ГРУППА */
		
		
		$qb->join('event', \App\Module\Users\Groups\Group\Entity\Group::TABLE, 'groups', 'groups.id = event.group_id');
		
		
		$qb->addSelect('group_event.sort');
		$qb->join('groups', \App\Module\Users\Groups\Group\Entity\Event\GroupEvent::TABLE, 'group_event', 'group_event.id = groups.event');
		
		$qb->addSelect('trans.name');
		$qb->addSelect('trans.description');
		$qb->join(
			'group_event',
			\App\Module\Users\Groups\Group\Entity\Trans\GroupTrans::TABLE,
			'trans',
			'trans.event = group_event.id AND trans.local = :local'
		);
		$qb->setParameter('local', $this->local, Locale::TYPE);
		
		
		/* Поиск */
		if($search->query)
		{
			$search->query = mb_strtolower($search->query);
			
			$qb->andWhere('LOWER(trans.id) LIKE :query');
			$qb->orWhere('LOWER(trans.id) LIKE :query');
			
			$qb->andWhere('LOWER(trans.name) LIKE :query');
			$qb->orWhere('LOWER(trans.name) LIKE :query');
			
			$qb->andWhere('LOWER(trans.description) LIKE :query');
			$qb->orWhere('LOWER(trans.description) LIKE :query');
			
			$qb->setParameter('query', '%'.$search->query.'%');
			$qb->setParameter('rus', '%'.$this->switcher->toRus($search->query, true).'%');
			$qb->setParameter('eng', '%'.$this->switcher->toEng($search->query, true).'%');
		}
		
		return $this->paginator->fetchAllAssociative($qb);
		
		//return $qb;
	}
	
}