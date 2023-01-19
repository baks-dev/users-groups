<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;


use App\Module\Users\Groups\Users\Type\Event\CheckUsersEventType;
use App\Module\Users\Groups\Users\Type\Event\CheckUsersEventUid;
use Symfony\Config\DoctrineConfig;

return static function (ContainerConfigurator $container, DoctrineConfig $doctrine)
{
	
    $doctrine->dbal()->type(CheckUsersEventUid::TYPE)->class(CheckUsersEventType::class);
    
    $emDefault = $doctrine->orm()->entityManager('default');
    
    $emDefault->autoMapping(true);
    $emDefault->mapping('GroupCheckUsers')
      ->type('attribute')
      ->dir('%kernel.project_dir%/src/Module/Users/Groups/Users/Entity')
      ->isBundle(false)
      ->prefix('App\Module\Users\Groups\Users\Entity')
      ->alias('GroupCheckUsers');
};