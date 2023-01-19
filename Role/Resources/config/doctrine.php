<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Module\Users\Groups\Role\Type\Event\RoleEventType;
use App\Module\Users\Groups\Role\Type\Event\RoleEventUid;
use App\Module\Users\Groups\Role\Type\RolePrefix\RolePrefix;
use App\Module\Users\Groups\Role\Type\RolePrefix\RolePrefixType;
use App\Module\Users\Groups\Role\Type\Voter\RoleVoterType;
use App\Module\Users\Groups\Role\Type\Voter\RoleVoterUid;
use App\Module\Users\Groups\Role\Type\VoterPrefix\VoterPrefix;
use App\Module\Users\Groups\Role\Type\VoterPrefix\VoterPrefixType;

use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine)
{
    
    $doctrine->dbal()->type(RoleEventUid::TYPE)->class(RoleEventType::class);
    $doctrine->dbal()->type(RoleVoterUid::TYPE)->class(RoleVoterType::class);
    $doctrine->dbal()->type(RolePrefix::TYPE)->class(RolePrefixType::class);
    $doctrine->dbal()->type(VoterPrefix::TYPE)->class(VoterPrefixType::class);
    
    $emDefault = $doctrine->orm()->entityManager('default');
    
    $emDefault->autoMapping(true);
    $emDefault->mapping('GroupRole')
      ->type('attribute')
      ->dir('%kernel.project_dir%/src/Module/Users/Groups/Role/Entity')
      ->isBundle(false)
      ->prefix('App\Module\Users\Groups\Role\Entity')
      ->alias('GroupRole');
};