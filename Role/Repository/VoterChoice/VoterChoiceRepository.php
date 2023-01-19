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

namespace App\Module\Users\Groups\Role\Repository\VoterChoice;

use App\Module\Users\Groups\Role\Entity;
use App\Module\Users\Groups\Role\Type\RolePrefix\RolePrefix;
use App\Module\Users\Groups\Role\Type\VoterPrefix\VoterPrefix;
use App\Module\Users\Groups\Role\Repository\VoterChoice\VoterChoiceInterface;
use App\System\Services\EntityEvent\EntityEventInterface;
use App\System\Type\Locale\Locale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

final class VoterChoiceRepository implements VoterChoiceInterface
{
    
    private EntityManagerInterface $entityManager;
    private Locale $local;
    
    public function __construct(EntityManagerInterface $entityManager,  TranslatorInterface $translator) {
        
        $this->entityManager = $entityManager;
        $this->local = new Locale($translator->getLocale());
    }
    
    public function get(RolePrefix $role) : mixed
    {
        $qb = $this->entityManager->createQueryBuilder();
        
        $select = sprintf('new %s(voter.voter, trans.name)', VoterPrefix::class);
    
        $qb->select($select);
        $qb->from(\App\Module\Users\Groups\Role\Entity\Role::class, 'role');
        $qb->join(\App\Module\Users\Groups\Role\Entity\Event\RoleEvent::class, 'event', 'WITH', 'event.id = role.event');
        $qb->join(\App\Module\Users\Groups\Role\Entity\Voters\RoleVoter::class, 'voter', 'WITH', 'voter.event = role.event');
        $qb->join(\App\Module\Users\Groups\Role\Entity\Voters\Trans\VoterTrans::class, 'trans', 'WITH', 'trans.voter = voter.id AND trans.local = :local');
        $qb->setParameter('local', $this->local, Locale::TYPE);
        
        $qb->where('role.id = :role');
        $qb->setParameter('role', $role, RolePrefix::TYPE);
    
        return $qb->getQuery()->getResult();
    }
    
}