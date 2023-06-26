<?php
/*
 *  Copyright 2022-2023.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Users\Groups\Users\Listeners\Entity;

use Doctrine\ORM\Events;
use BaksDev\Users\User\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use BaksDev\Users\Groups\Users\Repository\RoleByUser\RoleByUserInterface;

#[AsEntityListener(event: Events::postLoad, method: 'postLoad', entity: User::class)]
final class UserListener
{
    private RoleByUserInterface $roleByUser;

    public function __construct(RoleByUserInterface $roleByUser)
    {
        $this->roleByUser = $roleByUser;
    }

    public function postLoad(User $data, LifecycleEventArgs $event): void
    {
        // По умолчанию Все авторизованные пользователи имеют роль ROLE_USER
        $roles[] = 'ROLE_USER';

        $userRoles = $this->roleByUser->fetchAllRoleUser($data->getId());

        if ($userRoles)
        {
            $roles[] = 'ROLE_ADMINISTRATION';
        }

        array_walk_recursive($userRoles, static function ($value) use (&$roles): void {
            if ($value)
            {
                $roles[] = $value;
            }
        }, $roles);

        $data->setRole(array_unique($roles));
    }
}
