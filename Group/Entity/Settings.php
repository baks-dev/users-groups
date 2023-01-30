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

namespace BaksDev\Users\Groups\Group\Entity;

use BaksDev\Users\Groups\Group\Type\Settings\GroupSettings;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/* Настройки сущности Group */


#[ORM\Entity]
#[ORM\Table(name: 'users_group_settings')]
class Settings
{
	public const TABLE = 'users_group_settings';
	
	/** ID */
	#[ORM\Id]
	#[ORM\Column(type: GroupSettings::TYPE)]
	private GroupSettings $id;
	
	/** Очищать корзину старше n дней */
	#[ORM\Column(name: 'settings_truncate', type: Types::SMALLINT, length: 3, nullable: false)]
	private int $settingsTruncate = 31;
	
	/** Очищать события старше n дней */
	#[ORM\Column(name: 'settings_history', type: Types::SMALLINT, length: 3, nullable: false)]
	private int $settingsHistory = 31;
	
	
	public function __construct(int $settingsTruncate, int $settingsHistory)
	{
		$this->id = new GroupSettings();
		$this->settingsTruncate = $settingsTruncate;
		$this->settingsHistory = $settingsHistory;
	}
	
}
