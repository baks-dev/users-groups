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

namespace App\Module\Users\Groups\Group\Repository\AllGroups;

use App\Module\Users\Groups\Group\Entity;
use App\Module\Users\Groups\Group\Repository\AllGroups\AllGroupsInterface;
use App\System\Form\Search\SearchDTO;
use App\System\Services\Paginator\PaginatorInterface;
use App\System\Services\Switcher\Switcher;
use App\System\Type\Locale\Locale;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;
use function App\Module\Users\Groups\Group\Repository\AllGroups\mb_strtolower;

final class AllGroupsQuery implements AllGroupsInterface
{
    
    private Connection $connection;
    private Switcher $switcher;
    
//    public function __construct(Connection $connection, TranslatorInterface $translator, Switcher $switcher)
//    {
//        $this->connection = $connection;
//        $this->local = new Locale($translator->getLocale());
//        $this->switcher = $switcher;
//    }
	private PaginatorInterface $paginator;
	
	
	public function __construct(Connection $connection, TranslatorInterface $translator, Switcher $switcher, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->local = new Locale($translator->getLocale());
        $this->switcher = $switcher;
		$this->paginator = $paginator;
	}
	
	
    public function get(SearchDTO $search) : PaginatorInterface
    {
        $qb = $this->connection->createQueryBuilder();
        
        $qb->select('groups.id');
        $qb->addSelect('groups.event');
        $qb->from(\App\Module\Users\Groups\Group\Entity\Group::TABLE, 'groups');
	
		
        $qb->addSelect('event.sort');
        $qb->join('groups', \App\Module\Users\Groups\Group\Entity\Event\GroupEvent::TABLE, 'event', 'event.id = groups.event');
	
		
		
        $qb->addSelect('trans.name');
        $qb->addSelect('trans.description');
        $qb->join(
          'event',
          \App\Module\Users\Groups\Group\Entity\Trans\GroupTrans::TABLE,
          'trans',
          'trans.event = event.id AND trans.local = :local');
        $qb->setParameter('local', $this->local, Locale::TYPE);
	

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
			
			$qb->andWhere('('.$searcher->getQueryPart('where').')' );
			$qb->setParameter('query', '%'.$this->switcher->toRus($search->query).'%');
			$qb->setParameter('switcher', '%'.$this->switcher->toEng($search->query).'%');
        }
        
        $qb->orderBy('event.sort');
		
		
	
		return $this->paginator->fetchAllAssociative($qb);

        //return $qb;
    }
    
}