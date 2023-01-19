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

namespace App\Module\Users\Groups\Role\Repository\RoleChoice;

use App\Module\Users\Groups\Role\Entity;
use App\Module\Users\Groups\Role\Repository\RoleChoice\RoleChoiceInterface;
use App\Module\Users\Groups\Role\Type\RolePrefix\RolePrefix;
use App\System\Type\Locale\Locale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RoleChoiceRepository implements RoleChoiceInterface
{
    
    private EntityManagerInterface $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
    
        $this->entityManager = $entityManager;
        $this->local = new Locale($translator->getLocale());
    }
    
    
    public function get()
    {
        $qb = $this->entityManager->createQueryBuilder();
        
        $select = sprintf('new %s(event.role, trans.name)', RolePrefix::class);
        
        $qb->select($select);
        $qb->from(\App\Module\Users\Groups\Role\Entity\Role::class, 'role');
        $qb->join(\App\Module\Users\Groups\Role\Entity\Event\RoleEvent::class, 'event', 'WITH', 'event.id = role.event');
        $qb->join(\App\Module\Users\Groups\Role\Entity\Trans\RoleTrans::class, 'trans', 'WITH', 'trans.event = event.id AND trans.local = :local');
        $qb->setParameter('local', $this->local, Locale::TYPE);
        
      return $qb->getQuery()->getResult();
    }
    
}