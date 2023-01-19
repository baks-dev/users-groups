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

namespace App\Module\Users\Groups\Group\Entity\Quota;

use App\Module\Users\Entity\User;
use App\Module\Users\Groups\Group\Entity\Event\GroupEvent;
use App\Module\Users\Groups\Group\Entity\Quota\GroupQuotaInterface;
use App\Module\Users\Groups\Group\Type\Event\GroupEventUid;
use App\Module\Users\Groups\Repository\Group\Modify\GroupModifyRepository;
use App\System\Entity\EntityEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* Ограничения Group */

#[ORM\Entity]
#[ORM\Table(name: 'users_group_quota')]
class GroupQuota  extends EntityEvent
{
    public const TABLE = 'users_group_quota';

    /** ID события */
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'quota', targetEntity: GroupEvent::class)]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    protected GroupEvent $event;
    
    /** Лимит дискового пространства (Mb) */
    #[ORM\Column(name: 'size', type: Types::SMALLINT, length: 5, nullable: true)]
    protected ?int $size = null;
    
    /** Лимит макс. размера одного файла (Mb) */
    #[ORM\Column(name: 'filesize', type: Types::SMALLINT, length: 5, nullable: true)]
    protected ?int $filesize = null;
    
    /** Лимит макс. числа файлов */
    #[ORM\Column(name: 'files', type: Types::SMALLINT, length: 5, nullable: true)]
    protected ?int $files = null;
    
    public function __construct(GroupEvent $event)
    {
        $this->event = $event;
    }
    
    public function getDto($dto) : mixed
    {
        if($dto instanceof GroupQuotaInterface)
        {
            return parent::getDto($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    public function setEntity($dto) : mixed
    {
        if($dto instanceof GroupQuotaInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}
