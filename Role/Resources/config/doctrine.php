<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Users\Groups\Role\Type\Event\RoleEventType;
use BaksDev\Users\Groups\Role\Type\Event\RoleEventUid;
use BaksDev\Users\Groups\Role\Type\RolePrefix\RolePrefix;
use BaksDev\Users\Groups\Role\Type\RolePrefix\RolePrefixType;
use BaksDev\Users\Groups\Role\Type\Voter\RoleVoterType;
use BaksDev\Users\Groups\Role\Type\Voter\RoleVoterUid;
use BaksDev\Users\Groups\Role\Type\VoterPrefix\VoterPrefix;
use BaksDev\Users\Groups\Role\Type\VoterPrefix\VoterPrefixType;

use Symfony\Config\DoctrineConfig;

return static function(DoctrineConfig $doctrine){
	
	$doctrine->dbal()->type(RoleEventUid::TYPE)->class(RoleEventType::class);
	$doctrine->dbal()->type(RoleVoterUid::TYPE)->class(RoleVoterType::class);
	$doctrine->dbal()->type(RolePrefix::TYPE)->class(RolePrefixType::class);
	$doctrine->dbal()->type(VoterPrefix::TYPE)->class(VoterPrefixType::class);
	
	$emDefault = $doctrine->orm()->entityManager('default');
	
	$emDefault->autoMapping(true);
	$emDefault->mapping('GroupRole')
		->type('attribute')
		->dir(__DIR__.'/../../Entity')
		->isBundle(false)
		->prefix('BaksDev\Users\Groups\Role\Entity')
		->alias('GroupRole')
	;
};