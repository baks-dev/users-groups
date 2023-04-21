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

namespace BaksDev\Users\Groups\Users\DataFixtures\Security\Role;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Users\Groups\Role\Entity\Event\RoleEventInterface;
use BaksDev\Users\Groups\Role\Type\Event\RoleEventUid;
use BaksDev\Users\Groups\Role\Type\RolePrefix\RolePrefix;
use BaksDev\Users\Groups\Role\Type\VoterPrefix\VoterPrefix;
use BaksDev\Users\Groups\Users\DataFixtures\Security\Role\Trans\RoleTransDTO;
use BaksDev\Users\Groups\Users\DataFixtures\Security\Role\Voter\RoleVoterDTO;
use Doctrine\Common\Collections\ArrayCollection;

final class RoleDTO implements RoleEventInterface
{
    public const ROLE_PREFIX = 'ROLE_CHECK_USERS';

    public const ROLE_NAME = [
        'ru' => 'Пользователи в группах',
        'en' => 'Users in groups',
    ];

    public const ROLE_DESC = [
        'ru' => 'Пользователи, которые состоят в какой либо из групп',
        'en' => 'Users who are members of any of the groups',
    ];

    /** Идентификатор */
    private ?RoleEventUid $id = null;

    /** Префикс Роли */
    private RolePrefix $role;

    /** Настройки локали */
    private ArrayCollection $translate;

    /** Правила роли */
    private ArrayCollection $voter;

    public function __construct()
    {
        $this->translate = new ArrayCollection();
        $this->getTranslate();

        $this->voter = new ArrayCollection();
        $this->getVoter();

        $this->role = new RolePrefix(self::ROLE_PREFIX);
    }

    public function getEvent(): ?RoleEventUid
    {
        return $this->id;
    }

    public function setId(RoleEventUid $id): void
    {
        $this->id = $id;
    }

    public function getRole(): RolePrefix
    {
        return $this->role;
    }

    // TRANSLATE

    public function getTranslate(): ArrayCollection
    {
        // Вычисляем расхождение и добавляем неопределенные локали
        foreach (Locale::diffLocale($this->translate) as $locale) {
            $TransFormDTO = new RoleTransDTO();
            $TransFormDTO->setLocal($locale);
            $TransFormDTO->setName(self::ROLE_NAME[(string) $locale]);
            $TransFormDTO->setDescription(self::ROLE_DESC[(string) $locale]);
            $this->addTranslate($TransFormDTO);
        }

        return $this->translate;
    }

    /**
     * @param RoleTransDTO $translate
     */
    public function addTranslate(
        Trans\RoleTransDTO $translate,
    ): void {
        $this->translate->add($translate);
    }

    // VOTER

    public function getVoter(): ArrayCollection
    {
        // TODO return array_diff(self::cases(), $search);

        if ($this->voter->isEmpty()) {
            foreach (RoleVoterDTO::VOTERS as $prefix => $voter) {
                $RoleVoterDTO = new RoleVoterDTO();
                $RoleVoterDTO->setVoter(new VoterPrefix(self::ROLE_PREFIX.'_'.$prefix));
                $RoleVoterDTO->setKey($prefix);
                $this->addVoter($RoleVoterDTO);
            }
        }

        return $this->voter;
    }

    public function addVoter(Voter\RoleVoterDTO $voter): void
    {
        $this->voter->add($voter);
    }
}
