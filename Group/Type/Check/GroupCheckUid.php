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

namespace BaksDev\Users\Groups\Group\Type\Check;

use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

final class GroupCheckUid
{
	public const TYPE = 'group_check_id';
	
	private Uuid $value;
	
	private ?string $name;
	
	
	public function __construct(AbstractUid|string|null $value = null, string $name = null)
	{
		if($value === null)
		{
			$value = Uuid::v7();
		}
		
		else if(is_string($value))
		{
			$value = new UuidV7($value);
		}
		
		$this->value = $value;
		
		$this->name = $name;
	}
	
	
	public function __toString() : string
	{
		return $this->value;
	}
	
	
	public function getValue() : AbstractUid
	{
		return $this->value;
	}
	
	
	/**
	 * @return string|null
	 */
	public function getName() : ?string
	{
		return $this->name;
	}
	
}