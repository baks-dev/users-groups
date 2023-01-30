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

namespace BaksDev\Users\Groups\Role\Type\RolePrefix;

use InvalidArgumentException;

final class RolePrefix
{
	public const TYPE = 'role_prefix';
	
	private $value;
	
	private ?string $name;
	
	
	public function __construct(?string $value = null, string $name = null)
	{
		if(empty($value))
		{
			throw new InvalidArgumentException('You need to pass a value Role Prefix');
		}
		
		if(!preg_match('/ROLE_(\w{1,10})/', $value))
		{
			throw new InvalidArgumentException('Incorrect Role Prefix.');
		}
		
		$this->value = \mb_strtoupper($value);
		$this->name = $name;
	}
	
	
	public function __toString() : string
	{
		return $this->value;
	}
	
	
	public function getValue() : string
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