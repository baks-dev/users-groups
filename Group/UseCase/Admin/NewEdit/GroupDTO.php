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

namespace App\Module\Users\Groups\Group\UseCase\Admin\NewEdit;

use App\Module\Users\Groups\Group\Entity\Event\GroupEventInterface;
use App\Module\Users\Groups\Group\Entity\Trans\GroupTransInterface;
use App\Module\Users\Groups\Group\Type\Event\GroupEventUid;
use App\Module\Users\Groups\Group\Type\Prefix\GroupPrefix;
use App\Module\Users\Groups\Group\UseCase\Admin\NewEdit\Quota\GroupQuotaDTO;
use App\System\Type\Locale\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class GroupDTO implements GroupEventInterface
{
    /** ID события */
    #[Assert\Uuid]
    private ?GroupEventUid $id = null;
    
    /** Префикс группы  */
    #[Assert\NotBlank]
    private GroupPrefix $group;
    
    /** Сортировка */
    #[Assert\Range(min: 0, max: 999)]
    #[Assert\NotBlank]
    private int $sort = 500;
    
    /** Настройки ограничений группы */
    #[Assert\Valid]
    private GroupQuotaDTO $quota;
    
    /** Роли группы */
    #[Assert\Valid]
    protected ArrayCollection $role;
    
    /** Настройки локали */
    #[Assert\Valid]
    private ArrayCollection $translate;
    
    public function __construct()
    {
        $this->quota = new GroupQuotaDTO();
        $this->translate = new ArrayCollection();
        $this->role = new ArrayCollection();
    }
    
    public function getEvent() : ?GroupEventUid
    {
        return $this->id;
        
    }
    
    public function setId(GroupEventUid $id) : void
    {
        $this->id = $id;
    }
    
    /* SORT */
    
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
    
    
    /* TRANSLATE */
    
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
            $GroupTransDTO = new \App\Module\Users\Groups\Group\UseCase\Admin\NewEdit\Trans\GroupTransDTO();
            $GroupTransDTO->setLocal($locale);
            $this->addTranslate($GroupTransDTO);
        }
        
        return $this->translate;
    }
    
    /** Добавляем перевод категории
     *
     * @param \App\Module\Users\Groups\Group\UseCase\Admin\NewEdit\Trans\GroupTransDTO $trans
     *
     * @return void
     */
    public function addTranslate(\App\Module\Users\Groups\Group\UseCase\Admin\NewEdit\Trans\GroupTransDTO $trans) : void
    {
        if(!$this->translate->contains($trans))
        {
            $this->translate[] = $trans;
        }
    }
    
//    public function getTranslateClass() : GroupTransInterface
//    {
//        return new Trans\GroupTransDTO();
//    }
    
    /* QUOTA */
    
    /**
     * @return GroupQuotaDTO
     */
    public function getQuota() : GroupQuotaDTO
    {
        return $this->quota;
    }
    
    /**
     * @param GroupQuotaDTO $quota
     */
    public function setQuota(GroupQuotaDTO $quota) : void
    {
        $this->quota = $quota;
    }
    
//    public function getQuotaClass() : GroupQuotaDTO
//    {
//        return new GroupQuotaDTO();
//    }
    
    /* GROUP */
    
    /**
     * @return GroupPrefix
     */
    public function getGroup() : GroupPrefix
    {
        return $this->group;
    }
    
    /**
     * @param GroupPrefix $group
     */
    public function setGroup(GroupPrefix $group) : void
    {
        $this->group = $group;
    }
    
    /* role */
    
    /**
     * @return ArrayCollection
     */
    public function getRole() : ArrayCollection
    {
        return $this->role;
    }

    public function addRole(\App\Module\Users\Groups\Group\UseCase\Admin\NewEdit\CheckRole\CheckRoleDTO $role) : void
    {
        $this->role->add($role);
    }
    
    public function removeRole(\App\Module\Users\Groups\Group\UseCase\Admin\NewEdit\CheckRole\CheckRoleDTO $role) : void
    {
        $this->role->removeElement($role);
    }
    
//    public function getRoleClass() : CheckRole\CheckRoleDTO
//    {
//        return new CheckRole\CheckRoleDTO();
//    }
    
}
