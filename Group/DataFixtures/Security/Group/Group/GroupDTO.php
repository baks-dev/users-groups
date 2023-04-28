<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

namespace BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group\Quota\GroupQuotaDTO;
use BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group\Trans\GroupTransDTO;
use BaksDev\Users\Groups\Group\Entity\Event\GroupEventInterface;
use BaksDev\Users\Groups\Group\Entity\Trans\GroupTransInterface;
use BaksDev\Users\Groups\Group\Type\Event\GroupEventUid;
use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefix;
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

    public function getEvent(): ?GroupEventUid
    {
        return $this->id;
    }

    public function setId(GroupEventUid $id): void
    {
        $this->id = $id;
    }

    // sort

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    // translate

    public function setTranslate(ArrayCollection $trans): void
    {
        $this->translate = $trans;
    }

    public function getTranslate(): ArrayCollection
    {
        // Вычисляем расхождение и добавляем неопределенные локали
        foreach (Locale::diffLocale($this->translate) as $locale) {
            $GroupTransDTO = new GroupTransDTO();
            $GroupTransDTO->setLocal($locale);
            $this->addTranslate($GroupTransDTO);
        }

        return $this->translate;
    }

    /** Добавляем перевод категории.
     *
     * @param GroupTransDTO $trans
     */
    public function addTranslate(
        Trans\GroupTransDTO $trans,
    ): void {
        if (!$this->translate->contains($trans)) {
            $this->translate[] = $trans;
        }
    }

    /** Метод для инициализации и маппинга сущности на DTO в коллекции  */
    public function getTranslateClass(): GroupTransInterface
    {
        return new GroupTransDTO();
    }

    public function getQuota(): GroupQuotaDTO
    {
        return $this->quota;
    }

    public function setQuota(GroupQuotaDTO $quota): void
    {
        $this->quota = $quota;
    }

    /** Метод для инициализации и маппинга сущности на DTO в коллекции  */
    public function getQuotaClass(): GroupQuotaDTO
    {
        return new GroupQuotaDTO();
    }

    public function getGroup(): GroupPrefix
    {
        return $this->group;
    }
}
