<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;


return static function (ContainerConfigurator $configurator)
{
    $services = $configurator->services()
      ->defaults()
      ->autowire()      // Automatically injects dependencies in your services.
      ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;
    

//    $services->load('App\Module\Users\Groups\Users\DataFixtures\\', '../../DataFixtures')
//      ->exclude('../../DataFixtures/**/*DTO.php');


};

