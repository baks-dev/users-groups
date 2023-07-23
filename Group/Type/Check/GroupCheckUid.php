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

use BaksDev\Core\Type\UidType\Uid;
use Symfony\Component\Uid\AbstractUid;

final class GroupCheckUid extends Uid
{
    public const TEST = '0188a9a5-e79a-7e75-8b77-0d81f34e9f1a';
    
	public const TYPE = 'group_check';
	

	private ?string $name;
	
	
	public function __construct(AbstractUid|string|null $value = null, string $name = null)
	{
        parent::__construct($value);
		$this->name = $name;
	}

	public function getName() : ?string
	{
		return $this->name;
	}
	
}