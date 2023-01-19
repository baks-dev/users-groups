<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;


return static function (ContainerConfigurator $configurator)
{

    $services = $configurator->services()
      ->defaults()
      ->autowire()      // Automatically injects dependencies in your services.
      ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;
    
//    $services->load('App\Module\Users\Groups\Group\Controller\\', '../../Controller')
//      ->tag('controller.service_arguments');
    
    $services->load('App\Module\Users\Groups\Role\Repository\\', '../../Repository');
    
    $services->load('App\Module\Users\Groups\Role\UseCase\\', '../../UseCase')
      ->exclude('../../UseCase/**/*DTO.php');
    

    
//    $services->load('App\Module\Users\Groups\GroupHandler\\', '../../Handler')
//      //->exclude('../../Handler/**/*Command.php')
//      ->tag('controller.service_arguments');

};

