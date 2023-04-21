<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()      // Automatically injects dependencies in your services.
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    $namespace = 'BaksDev\Users\Groups\Users';

    $services->load($namespace.'\DataFixtures\\', __DIR__.'/../../DataFixtures')
        ->exclude('../../DataFixtures/**/*DTO.php')
    ;
};
