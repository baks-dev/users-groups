<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Module\Users\Groups\Group\Entity\Group;
use App\Module\Users\Groups\Users\Entity\Modify\CheckUserModify;
use App\Module\Users\Groups\Users\EntityListeners;
use App\Module\Users\User\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

//use App\Module\Users\Entity\User;

return static function (ContainerConfigurator $configurator)
{
    $services = $configurator->services()
      ->defaults()
      ->autowire()
      ->autoconfigure();
    
    /** EntityListeners */
    $services->set(\App\Module\Users\Groups\Users\EntityListeners\ModifyListener::class)
      ->class(\App\Module\Users\Groups\Users\EntityListeners\ModifyListener::class)
      ->tag(
        'doctrine.orm.entity_listener',
        ['event' => 'prePersist', 'lazy' => true, 'entity' => CheckUserModify::class]);
    
	
    $services->set(\App\Module\Users\Groups\Users\EntityListeners\GroupListener::class)
      ->class(\App\Module\Users\Groups\Users\EntityListeners\GroupListener::class)
      ->tag(
        'doctrine.orm.entity_listener',
        ['event' => 'preUpdate', 'lazy' => true, 'entity' => Group::class]);
	

	$services->set(\App\Module\Users\Groups\Users\EntityListeners\UserListener::class)
		->class(\App\Module\Users\Groups\Users\EntityListeners\UserListener::class)
		->tag(
			'doctrine.orm.entity_listener',
			['event' => 'postLoad', 'lazy' => true, 'entity' => User::class])
		//->arg()
	;
	
};


