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

namespace BaksDev\Users\Groups\Group\DataFixtures\Security\Group\Group\Quota;

use BaksDev\Users\Groups\Group\Entity\Quota\GroupQuota;
use BaksDev\Users\Groups\Group\Entity\Quota\GroupQuotaInterface;

/**
 * Builder instance for {@see GroupQuota}.
 */
class GroupQuotaDTO implements GroupQuotaInterface
{
    /** Лимит дискового пространства (Mb) */
    private ?int $size = null;
    
    /** Лимит макс. размера одного файла (Mb) */
    private ?int $filesize = null;
    
    /** Лимит макс. числа файлов */
    private ?int $files = null;
    
    
    /**
     * @return int|null
     */
    public function getSize() : ?int
    {
        return $this->size;
    }

    /**
     * @return int|null
     */
    public function getFilesize() : ?int
    {
        return $this->filesize;
    }
    
    /**
     * @return int|null
     */
    public function getFiles() : ?int
    {
        return $this->files;
    }
}
