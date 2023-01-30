<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Users\Groups\Group\Repository\ExistGroup;

use BaksDev\Users\Groups\Group\Entity\Group;
use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefix;
use BaksDev\Users\Groups\Group\Repository\ExistGroup\ExistGroupInterface;
use Doctrine\DBAL\Connection;

final class ExistGroupQuery implements ExistGroupInterface
{
	
	private Connection $connection;
	
	
	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}
	
	
	public function get(GroupPrefix $prefix) : bool
	{
		$qb = $this->connection->createQueryBuilder();
		
		$qb->select('users_group.id');
		$qb->from(Group::TABLE, 'users_group');
		$qb->where('users_group.id = :prefix');
		
		$exist = $this->connection->createQueryBuilder();
		$exist->select('EXISTS ('.$qb->getSQL().') ');
		$exist->setParameter('prefix', $prefix);
		
		return (bool) $exist->fetchOne();
	}
	
}