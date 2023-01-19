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

namespace App\Module\Users\Groups\Users\UseCase\Admin\Delete;

use App\Module\Users\Groups\Group\Type\Prefix\GroupPrefix;
use App\Module\Users\Groups\Users\Entity\CheckUserInterface;
use App\Module\Users\Groups\Users\Entity\Event\CheckUsersEventInterface;
use App\Module\Users\Groups\Users\Entity\Modify\CheckUserModifyInterface;
use App\Module\Users\Groups\Users\Type\Event\CheckUsersEventUid;
use App\Module\Users\User\Type\Id\UserUid;
use Symfony\Component\Validator\Constraints as Assert;

final class DeleteCheckUserDTO implements CheckUsersEventInterface
{
    /** ID */
    private readonly CheckUsersEventUid $id;

    private \App\Module\Users\Groups\Users\UseCase\Admin\Delete\Modify\ModifyDTO $modify;
    
    public function __construct() {
        $this->modify = new \App\Module\Users\Groups\Users\UseCase\Admin\Delete\Modify\ModifyDTO();
    }
    
    /**
     * @param CheckUsersEventUid $id
     */
    public function setId(CheckUsersEventUid $id) : void
    {
        $this->id = $id;
    }
    
    /**
     * @return CheckUsersEventUid
     */
    public function getEvent() : CheckUsersEventUid
    {
        return $this->id;
    }

    
    /* MODIFY  */
    
    
    /**
     * @return \App\Module\Users\Groups\Users\UseCase\Admin\Delete\Modify\ModifyDTO
     */
    public function getModify() : \App\Module\Users\Groups\Users\UseCase\Admin\Delete\Modify\ModifyDTO
    {
        return $this->modify;
    }
    
    /** Метод для инициализации и маппинга сущности на DTO в коллекции  */
    public function getModifyClass() : CheckUserModifyInterface
    {
        return new \App\Module\Users\Groups\Users\UseCase\Admin\Delete\Modify\ModifyDTO();
    }
    
}

