<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Users\Groups\Users\Type\Event\CheckUsersEventType;
use BaksDev\Users\Groups\Users\Type\Event\CheckUsersEventUid;
use Symfony\Config\DoctrineConfig;

return static function(DoctrineConfig $doctrine) {
	
	$doctrine->dbal()->type(CheckUsersEventUid::TYPE)->class(CheckUsersEventType::class);
	
	$emDefault = $doctrine->orm()->entityManager('default');
	
	$emDefault->autoMapping(true);
	$emDefault->mapping('GroupCheckUsers')
		->type('attribute')
		->dir(__DIR__.'/../../Entity')
		->isBundle(false)
		->prefix('BaksDev\Users\Groups\Users\Entity')
		->alias('GroupCheckUsers')
	;
};