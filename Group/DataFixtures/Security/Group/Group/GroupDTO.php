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

namespace BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group;

use BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group\Quota\GroupQuotaDTO;
use BaksDev\Users\Groups\Group\Entity\Event\GroupEventInterface;
use BaksDev\Users\Groups\Group\Entity\Trans\GroupTransInterface;
use BaksDev\Users\Groups\Group\Type\Event\GroupEventUid;
use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefix;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class GroupDTO implements GroupEventInterface
{
	public const GROUP_PREFIX = 'ROLE_ADMIN';
	
	#[Assert\Uuid]
	private ?GroupEventUid $id = null;
	
	#[Assert\NotBlank]
	private GroupPrefix $group;
	
	#[Assert\NotBlank]
	private int $sort = 500;
	
	#[Assert\Valid]
	private ArrayCollection $translate;
	
	#[Assert\Valid]
	private GroupQuotaDTO $quota;
	
	
	public function __construct()
	{
		$this->quota = new GroupQuotaDTO();
		
		$this->translate = new ArrayCollection();
		$this->group = new GroupPrefix(self::GROUP_PREFIX);
	}
	
	
	public function getEvent() : ?GroupEventUid
	{
		return $this->id;
		
	}
	
	
	public function setId(GroupEventUid $id) : void
	{
		$this->id = $id;
	}
	
	/* sort */
	
	/**
	 * @return int
	 */
	public function getSort() : int
	{
		return $this->sort;
	}
	
	
	/**
	 * @param int $sort
	 */
	public function setSort(int $sort) : void
	{
		$this->sort = $sort;
	}
	
	
	/* translate */
	
	/**
	 * @param ArrayCollection $trans
	 */
	public function setTranslate(ArrayCollection $trans) : void
	{
		$this->translate = $trans;
	}
	
	
	/**
	 * @return ArrayCollection
	 */
	public function getTranslate() : ArrayCollection
	{
		
		/* Вычисляем расхождение и добавляем неопределенные локали */
		foreach(Locale::diffLocale($this->translate) as $locale)
		{
			$GroupTransDTO = new \BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group\Trans\GroupTransDTO();
			$GroupTransDTO->setLocal($locale);
			$this->addTranslate($GroupTransDTO);
		}
		
		return $this->translate;
	}
	
	
	/** Добавляем перевод категории
	 *
	 * @param \BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group\Trans\GroupTransDTO $trans
	 *
	 * @return void
	 */
	public function addTranslate(
		\BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group\Trans\GroupTransDTO $trans,
	) : void
	{
		if(!$this->translate->contains($trans))
		{
			$this->translate[] = $trans;
		}
	}
	
	
	/** Метод для инициализации и маппинга сущности на DTO в коллекции  */
	public function getTranslateClass() : GroupTransInterface
	{
		return new \BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group\Trans\GroupTransDTO();
	}
	
	
	/**
	 * @return \BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group\Quota\GroupQuotaDTO
	 */
	public function getQuota() : GroupQuotaDTO
	{
		return $this->quota;
	}
	
	
	/**
	 * @param \BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group\Quota\GroupQuotaDTO $quota
	 */
	public function setQuota(GroupQuotaDTO $quota) : void
	{
		$this->quota = $quota;
	}
	
	
	/** Метод для инициализации и маппинга сущности на DTO в коллекции  */
	public function getQuotaClass() : GroupQuotaDTO
	{
		return new GroupQuotaDTO();
	}
	
	
	/**
	 * @return GroupPrefix
	 */
	public function getGroup() : GroupPrefix
	{
		return $this->group;
	}
	
}
