<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Users\Groups\Group\DataFixtures\Security\RoleFixtures;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()      // Automatically injects dependencies in your services.
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    $services->load('BaksDev\Users\Groups\Group\DataFixtures\\', __DIR__.'/../../DataFixtures');

    $services->set(RoleFixtures::class)
        // inject all services tagged with app.handler as first argument
        ->arg('$roles', tagged_iterator('baks.security.role'))
        ->arg('$voters', tagged_iterator('baks.security.voter'))
    ;
};
