<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Users\Groups\Group\Type\Check\GroupCheckType;
use BaksDev\Users\Groups\Group\Type\Check\GroupCheckUid;
use BaksDev\Users\Groups\Group\Type\Event\GroupEventUid;
use BaksDev\Users\Groups\Group\Type\Event\GroupEventUidType;
use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefix;
use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefixType;
use BaksDev\Users\Groups\Group\Type\Settings\GroupSettings;
use BaksDev\Users\Groups\Group\Type\Settings\GroupSettingsType;

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
      ->prefix('BaksDev\Users\Groups\Group\Entity')
      ->alias('UserGroup');
};