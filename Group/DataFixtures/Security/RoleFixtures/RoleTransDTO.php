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

namespace BaksDev\Users\Groups\Group\DataFixtures\Security\RoleFixtures;

use BaksDev\Core\Type\Locale\LocaleEnum;
use BaksDev\Users\Groups\Role\Entity\Trans\RoleTransInterface;
use BaksDev\Core\Type\Locale\Locale;
use Symfony\Component\Validator\Constraints as Assert;

final class RoleTransDTO implements RoleTransInterface
{
    /** Локаль */
    #[Assert\NotBlank]
    private readonly Locale $local;

    /** Название роли */
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[\w \.\_\-\(\)\%]+$/iu')]
    private readonly ?string $name;

    /** Краткое описание */
    #[Assert\Regex(pattern: '/^[\w \.\_\-\(\)\%]+$/iu')]
    private  ?string $description = null;


    public function __construct(Locale $local)
    {
        $this->local = $local;
    }

    /** Локаль */

    public function getLocal(): Locale
    {
        return $this->local;
    }

    /** Название роли */

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /** Краткое описание */

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

}

