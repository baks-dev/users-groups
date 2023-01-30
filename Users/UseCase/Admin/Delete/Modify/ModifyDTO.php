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

namespace BaksDev\Users\Groups\Users\UseCase\Admin\Delete\Modify;

use BaksDev\Users\Groups\Users\Entity\Modify\CheckUserModifyInterface;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use Symfony\Component\Validator\Constraints as Assert;

final class ModifyDTO implements CheckUserModifyInterface
{
	/** Модификатор */
	private readonly ModifyAction $action;
	
	
	public function __construct()
	{
		$this->action = new ModifyAction(ModifyActionEnum::DELETE);
	}
	
	
	/**
	 * @return ModifyAction
	 */
	public function getAction() : ModifyAction
	{
		return $this->action;
	}
	
}

