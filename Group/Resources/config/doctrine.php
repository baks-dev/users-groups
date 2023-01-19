<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Module\Users\Groups\Group\Type\Check\GroupCheckType;
use App\Module\Users\Groups\Group\Type\Check\GroupCheckUid;
use App\Module\Users\Groups\Group\Type\Event\GroupEventUid;
use App\Module\Users\Groups\Group\Type\Event\GroupEventUidType;
use App\Module\Users\Groups\Group\Type\Prefix\GroupPrefix;
use App\Module\Users\Groups\Group\Type\Prefix\GroupPrefixType;
use App\Module\Users\Groups\Group\Type\Settings\GroupSettings;
use App\Module\Users\Groups\Group\Type\Settings\GroupSettingsType;

use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine)
{

    $doctrine->dbal()->type(GroupPrefix::TYPE)->class(GroupPrefixType::class);
    $doctrine->dbal()->type(GroupEventUid::TYPE)->class(GroupEventUidType::class);
    $doctrine->dbal()->type(GroupCheckUid::TYPE)->class(GroupCheckType::class);
    $doctrine->dbal()->type(GroupSettings::TYPE)->class(GroupSettingsType::class);
    
    $emDefault = $doctrine->orm()->entityManager('default');
    
    $emDefault->autoMapping(true);
    $emDefault->mapping('UserGroup')
      ->type('attribute')
      ->dir('%kernel.project_dir%/src/Module/Users/Groups/Group/Entity')
      ->isBundle(false)
      ->prefix('App\Module\Users\Groups\Group\Entity')
      ->alias('UserGroup');
};