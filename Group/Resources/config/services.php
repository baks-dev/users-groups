<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;


return static function (ContainerConfigurator $configurator)
{

    $services = $configurator->services()
      ->defaults()
      ->autowire()      // Automatically injects dependencies in your services.
      ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;
    
    $services->load('BaksDev\Users\Groups\Group\Controller\\', '../../Controller')
      ->tag('controller.service_arguments');
    
    $services->load('BaksDev\Users\Groups\Group\Repository\\', '../../Repository');
    
    $services->load('BaksDev\Users\Groups\Group\UseCase\\', '../../UseCase')
      ->exclude('../../UseCase/**/*DTO.php');
	
};

