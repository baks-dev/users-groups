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

declare(strict_types=1);

namespace BaksDev\Users\Groups\Group\DataFixtures\Security\RoleFixtures;

use BaksDev\Users\Groups\Group\DataFixtures\Security\RoleFixturesInterface;
use BaksDev\Users\Groups\Role\Entity\Event\RoleEventInterface;
use BaksDev\Users\Groups\Role\Type\Event\RoleEventUid;
use BaksDev\Users\Groups\Role\Type\RolePrefix\RolePrefix;
use BaksDev\Users\Groups\Role\Type\VoterPrefix\VoterPrefix;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

final class RoleDTO implements RoleEventInterface
{

    /** Идентификатор */
    private readonly ?RoleEventUid $id;

    /** Префикс Роли */
    #[Assert\NotBlank]
    private readonly RolePrefix $role;

    /** Настройки локали */
    #[Assert\Valid]
    private ArrayCollection $translate;

    /** Правила роли */
    #[Assert\Valid]
    private ArrayCollection $voter;


    public function __construct(RoleFixturesInterface $role)
    {
        $this->translate = new ArrayCollection();
        $this->voter = new ArrayCollection();
        $this->role = new RolePrefix($role->getRole());
    }


    public function getEvent(): ?RoleEventUid
    {
        return $this->id;
    }

    public function setId(?RoleEventUid $id): void
    {
        $this->id = $id;
    }

    /** Префикс Роли */

    public function getRole(): RolePrefix
    {
        return $this->role;
    }


    /* TRANSLATE */

    /**
     * @return ArrayCollection
     */
    public function getTranslate(): ArrayCollection
    {
        return $this->translate;
    }


    public function addTranslate(RoleTransDTO $translate,): void
    {
        $this->translate->add($translate);
    }


    /* VOTER */

    public function getVoter(): ArrayCollection
    {
        return $this->voter;
    }


    public function addVoter(Voter\VoterDTO $voter): void
    {
        $this->voter->add($voter);
    }

}